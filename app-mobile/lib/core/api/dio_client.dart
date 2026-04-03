import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'api_config.dart';
import 'photopro_interceptor.dart';

class DioClient {
  final Ref ref;
  late final Dio dio;

  DioClient(this.ref) {
    dio = Dio(
      BaseOptions(
        baseUrl: ApiConfig.baseUrl,
        connectTimeout: ApiConfig.timeout,
        receiveTimeout: ApiConfig.timeout,
        contentType: Headers.jsonContentType,
      ),
    );
    
    dio.interceptors.add(PhotoProInterceptor(ref));
  }
}
// Provider for Dio
final dioProvider = Provider<Dio>((ref) => DioClient(ref).dio);
