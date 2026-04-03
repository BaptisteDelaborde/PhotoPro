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
  void onError(DioException err, ErrorInterceptorHandler handler) {
    dev.log('API ERROR [${err.response?.statusCode}] !! ${err.requestOptions.uri}');
    
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
        details: {'raw': err.message},
      );
    }

    // Trigger global error UI
    ref.read(errorProvider.notifier).state = ErrorState(mappedError.message);

    super.onError(err.copyWith(error: mappedError), handler);
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
      default: return 'Une erreur inattendue est survenue.';
    }
  }
}
