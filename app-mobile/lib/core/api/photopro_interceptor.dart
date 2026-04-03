import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'dart:developer' as dev;
import '../../data/models/error.dart';
import '../../presentation/providers/error_provider.dart';

class PhotoProInterceptor extends Interceptor {
  final Ref ref;
  PhotoProInterceptor(this.ref);

  @override
  void onRequest(RequestOptions options, RequestInterceptorHandler handler) {
    dev.log('API REQUEST [${options.method}] -> ${options.uri}');
    super.onRequest(options, handler);
  }

  @override
  void onResponse(Response response, ResponseInterceptorHandler handler) {
    dev.log('API RESPONSE [${response.statusCode}] <- ${response.requestOptions.uri}');
    super.onResponse(response, handler);
  }

  @override
  Future<void> onError(DioException err, ErrorInterceptorHandler handler) async {
    dev.log('API ERROR [${err.response?.statusCode}] !! ${err.requestOptions.uri}');
    
    // Auto-retry on SocketException/Connection Reset (DioExceptionType.unknown)
    if (err.type == DioExceptionType.unknown || err.type == DioExceptionType.connectionTimeout) {
      int retryCount = err.requestOptions.extra['retries'] ?? 0;
      if (retryCount < 3) {
        err.requestOptions.extra['retries'] = retryCount + 1;
        dev.log('RETRYING connection (${retryCount + 1}/3)...');
        try {
          await Future.delayed(Duration(milliseconds: 500 * (retryCount + 1)));
          // fetch again with the exact same request using a temporary vanilla dio to avoid interceptor loop
          final retryDio = Dio();
          final response = await retryDio.fetch(
            err.requestOptions.copyWith(path: err.requestOptions.uri.toString())
          );
          return handler.resolve(response);
        } catch (e) {
          dev.log('RETRY FAILED: $e');
        }
      }
    }

    PhotoProError? mappedError;
    if (err.response?.data != null && err.response?.data is Map<String, dynamic>) {
      try {
        mappedError = PhotoProError.fromJson(err.response!.data);
      } catch (e) {
        dev.log('FAILED TO PARSE ERROR BODY: $e');
      }
    }

    if (mappedError == null) {
      final statusCode = err.response?.statusCode;
      mappedError = PhotoProError(
        code: _mapStatusCodeToErrorCode(statusCode),
        message: _mapStatusCodeToMessage(statusCode),
        details: {'raw': err.message ?? "Erreur réseau ou connexion perdue"},
      );
    }

    // Trigger global error UI
    ref.read(errorProvider.notifier).state = ErrorState(mappedError.message);

    super.onError(err.copyWith(error: mappedError, message: mappedError.message), handler);
  }

  String _mapStatusCodeToErrorCode(int? statusCode) {
    switch (statusCode) {
      case 403: return 'ERR_FORBIDDEN';
      case 404: return 'ERR_NOT_FOUND';
      case 429: return 'ERR_RATE_LIMIT';
      case 500: return 'ERR_SERVER_ERROR';
      default: return 'ERR_UNKNOWN';
    }
  }

  String _mapStatusCodeToMessage(int? statusCode) {
    switch (statusCode) {
      case 403: return 'Accès refusé. Vérifiez votre code.';
      case 404: return 'Ressource introuvable.';
      case 429: return 'Trop de requêtes. Réessayez plus tard.';
      case 500: return 'Erreur interne du serveur.';
      default: return 'Une erreur de connexion inattendue est survenue (le serveur a pu rejeter la requête).';
    }
  }
}
