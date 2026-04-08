import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:isar/isar.dart';
import '../../core/persistence/persistence_service.dart';
import '../../data/models/galerie_model.dart';
import '../providers/gallery_providers.dart';
import '../../core/api/api_config.dart';

class PhotoDetailScreen extends ConsumerStatefulWidget {
  final String galleryId;
  final String photoId;
  const PhotoDetailScreen({super.key, required this.galleryId, required this.photoId});

  @override
  ConsumerState<PhotoDetailScreen> createState() => _PhotoDetailScreenState();
}

class _PhotoDetailScreenState extends ConsumerState<PhotoDetailScreen> {
  final _nicknameController = TextEditingController();
  final _contentController = TextEditingController();
  bool _isSubmitting = false;

  @override
  void dispose() {
    _nicknameController.dispose();
    _contentController.dispose();
    super.dispose();
  }

  Future<void> _submitComment() async {
    FocusScope.of(context).unfocus();
    final nickname = _nicknameController.text.trim();
    final content = _contentController.text.trim();

    if (nickname.isEmpty || content.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Veuillez remplir tous les champs.')),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final repository = ref.read(galleryRepositoryProvider);
      await repository.postComment(
        galleryId: widget.galleryId,
        photoId: widget.photoId,
        nickname: nickname,
        content: content,
      );
      
      if (mounted) {
        _contentController.clear();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Commentaire ajouté !')),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur: $e')),
        );
      }
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final photosAsync = ref.watch(galleryPhotosProvider(widget.galleryId));
    final photo = photosAsync.valueOrNull?.items.firstWhere(
      (p) => p.remoteId == widget.photoId,
    );

    final gallery = PersistenceService.isar.galerieModels.filter()
      .remoteIdEqualTo(widget.galleryId)
      .findFirstSync();

    final bool isPrivate = !(gallery?.isPublic ?? true);

    String imageUrl = photo?.storageUrl ?? '';
    if (imageUrl.isNotEmpty && !imageUrl.startsWith('http')) {
      imageUrl = '${ApiConfig.baseUrl}/photos/${photo!.remoteId}/storage';
    }

    final httpIndex = imageUrl.indexOf('http', 4);
    if (httpIndex != -1) {
      imageUrl = imageUrl.substring(httpIndex);
    } else if (Theme.of(context).platform == TargetPlatform.android && imageUrl.contains('localhost')) {
      imageUrl = imageUrl.replaceAll('localhost', '10.0.2.2');
    }

    return Scaffold(
      appBar: AppBar(title: const Text('Photo')),
      body: SingleChildScrollView(
        child: Column(
          children: [
            Hero(
              tag: 'photo_${widget.photoId}',
              child: imageUrl.isEmpty
                  ? Container(
                      height: 200,
                      color: Colors.grey[300],
                      child: const Icon(Icons.broken_image, size: 50),
                    )
                  : Image.network(
                      imageUrl,
                      width: double.infinity,
                      fit: BoxFit.contain,
                      errorBuilder: (_, __, ___) => Container(
                        height: 200,
                        color: Colors.grey[300],
                        child: const Icon(Icons.broken_image, size: 50),
                      ),
                    ),
            ),
            if (isPrivate)
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Ajouter un commentaire', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 16),
                    TextField(
                      controller: _nicknameController,
                      decoration: const InputDecoration(
                        labelText: 'Pseudo',
                        border: OutlineInputBorder(),
                      ),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _contentController,
                      maxLines: 3,
                      decoration: const InputDecoration(
                        labelText: 'Votre commentaire',
                        border: OutlineInputBorder(),
                      ),
                    ),
                    const SizedBox(height: 16),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _isSubmitting ? null : _submitComment,
                        child: _isSubmitting ? const CircularProgressIndicator() : const Text('Envoyer'),
                      ),
                    ),
                  ],
                ),
              )
            else
              const Padding(
                padding: EdgeInsets.all(24.0),
                child: Text('Les commentaires sont désactivés pour les galeries publiques.',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: Colors.grey, fontStyle: FontStyle.italic),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
