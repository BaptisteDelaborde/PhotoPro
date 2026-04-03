import '../../data/models/galerie_model.dart';
import '../../data/models/photo_model.dart';
import '../../data/models/comment.dart';
import '../../data/models/paginated_response.dart';

abstract class GalleryRepository {
  Future<PaginatedResponse<GalerieModel>> fetchPublicGalleries({int limit = 20, int offset = 0});
  
  Future<GalerieModel> getGalleryByCode(String code);
  
  Future<PaginatedResponse<PhotoModel>> getGalleryPhotos(String galleryId, {int limit = 50, int offset = 0});
  
  Future<Commentaire> postComment({
    required String galleryId,
    required String photoId,
    required String nickname,
    required String content,
  });
}
