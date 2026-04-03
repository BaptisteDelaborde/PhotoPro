import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../core/api/dio_client.dart';
import '../../core/persistence/persistence_service.dart';
import '../../data/repositories/api_gallery_repository.dart';
import '../../domain/repositories/gallery_repository.dart';
import '../../data/models/galerie_model.dart';
import '../../data/models/photo_model.dart';
import '../../data/models/paginated_response.dart';

// Repository Provider
final galleryRepositoryProvider = Provider<GalleryRepository>((ref) {
  final dio = ref.watch(dioProvider);
  final isar = PersistenceService.isar;
  return ApiGalleryRepository(dio, isar);
});

// Public Galleries Provider
final publicGalleriesProvider = FutureProvider<PaginatedResponse<GalerieModel>>((ref) async {
  final repository = ref.watch(galleryRepositoryProvider);
  return repository.fetchPublicGalleries();
});

// Private Gallery Provider (US2)
final privateGalleryProvider = FutureProvider.family<GalerieModel, String>((ref, code) async {
  final repository = ref.watch(galleryRepositoryProvider);
  return repository.getGalleryByCode(code);
});

// Gallery Photos Provider (US3)
final galleryPhotosProvider = FutureProvider.family<PaginatedResponse<PhotoModel>, String>((ref, galleryId) async {
  final repository = ref.watch(galleryRepositoryProvider);
  return repository.getGalleryPhotos(galleryId);
});
