import 'package:dio/dio.dart';
import 'package:isar/isar.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../core/persistence/persistence_service.dart';
import '../../domain/repositories/gallery_repository.dart';
import '../models/galerie_model.dart';
import '../models/photo_model.dart';
import '../models/comment.dart';
import '../models/paginated_response.dart';

class ApiGalleryRepository implements GalleryRepository {
  final Dio _dio;
  final Isar _isar;
  final FlutterSecureStorage _secureStorage;

  ApiGalleryRepository(this._dio, this._isar, [this._secureStorage = PersistenceService.secureStorage]);

  @override
  Future<PaginatedResponse<GalerieModel>> fetchPublicGalleries({int limit = 20, int offset = 0}) async {
    try {
      final response = await _dio.get('/galeries/publiques', queryParameters: {
        'limit': limit,
        'offset': offset,
      });
      
      late PaginatedResponse<GalerieModel> paginatedResponse;
      if (response.data is List) {
        final list = (response.data as List)
            .map((json) => GalerieModel.fromJson(json as Map<String, dynamic>))
            .toList();
        paginatedResponse = PaginatedResponse<GalerieModel>(
          items: list,
          totalCount: list.length,
          limit: limit,
          offset: offset,
          hasNext: false,
        );
      } else {
        paginatedResponse = PaginatedResponse<GalerieModel>.fromJson(
          response.data,
          (json) => GalerieModel.fromJson(json as Map<String, dynamic>),
        );
      }

      // Cache the results
      await _isar.writeTxn(() async {
        await _isar.galerieModels.putAll(paginatedResponse.items);
      });

      return paginatedResponse;
    } catch (e) {
      // Offline fallback
      final cachedGalleries = await _isar.galerieModels.where().findAll();
      if (cachedGalleries.isNotEmpty) {
        return PaginatedResponse(
          items: cachedGalleries,
          totalCount: cachedGalleries.length,
          limit: limit,
          offset: offset,
          hasNext: false,
        );
      }
      rethrow;
    }
  }

  @override
  Future<GalerieModel> getGalleryByCode(String code) async {
    final response = await _dio.get('/galeries/code/$code');
    final gallery = GalerieModel.fromJson(response.data);
    
    // Save to cache
    await _isar.writeTxn(() async {
      await _isar.galerieModels.put(gallery);
    });

    // Save code securely
    await _secureStorage.write(key: 'gallery_code_${gallery.remoteId}', value: code);
    
    return gallery;
  }

  @override
  Future<PaginatedResponse<PhotoModel>> getGalleryPhotos(String galleryId, {int limit = 50, int offset = 0}) async {
    try {
      final response = await _dio.get('/galeries/$galleryId/photos', queryParameters: {
        'limit': limit,
        'offset': offset,
      });

      late PaginatedResponse<PhotoModel> paginatedResponse;
      final responseData = response.data;
      
      if (responseData == null || (responseData is String && responseData.isEmpty)) {
        paginatedResponse = PaginatedResponse<PhotoModel>(
          items: [],
          totalCount: 0,
          limit: limit,
          offset: offset,
          hasNext: false,
        );
      } else if (responseData is List) {
        final list = responseData
            .map((json) => PhotoModel.fromJson(json as Map<String, dynamic>))
            .toList();
        paginatedResponse = PaginatedResponse<PhotoModel>(
          items: list,
          totalCount: list.length,
          limit: limit,
          offset: offset,
          hasNext: false,
        );
      } else if (responseData is Map<String, dynamic>) {
        if (responseData.containsKey('photos')) {
          final list = (responseData['photos'] as List)
              .map((json) => PhotoModel.fromJson(json as Map<String, dynamic>))
              .toList();
          paginatedResponse = PaginatedResponse<PhotoModel>(
            items: list,
            totalCount: list.length,
            limit: limit,
            offset: offset,
            hasNext: false,
          );
        } else if (responseData.containsKey('items')) {
          paginatedResponse = PaginatedResponse<PhotoModel>.fromJson(
            responseData,
            (json) => PhotoModel.fromJson(json as Map<String, dynamic>),
          );
        } else {
          paginatedResponse = PaginatedResponse<PhotoModel>(
            items: [],
            totalCount: 0,
            limit: limit,
            offset: offset,
            hasNext: false,
          );
        }
      } else {
        paginatedResponse = PaginatedResponse<PhotoModel>(
          items: [],
          totalCount: 0,
          limit: limit,
          offset: offset,
          hasNext: false,
        );
      }

      await _isar.writeTxn(() async {
        await _isar.photoModels.putAll(paginatedResponse.items);
      });

      return paginatedResponse;
    } catch (e, stacktrace) {
      print("Error in getGalleryPhotos: $e");
      print(stacktrace);
      rethrow;
    }
  }

  @override
  Future<Commentaire> postComment({
    required String galleryId,
    required String photoId,
    required String nickname,
    required String content,
  }) async {
    final response = await _dio.post(
      '/galeries/$galleryId/photos/$photoId/commentaires',
      data: {
        'author_name': nickname,
        'content': content,
      },
    );
    
    return Commentaire.fromJson(response.data);
  }
}
