import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../providers/gallery_providers.dart';
import '../../core/api/api_config.dart';

class GalleryDetailScreen extends ConsumerWidget {
  final String galleryId;
  const GalleryDetailScreen({super.key, required this.galleryId});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final photosAsync = ref.watch(galleryPhotosProvider(galleryId));

    return Scaffold(
      appBar: AppBar(title: const Text('Photos de la Galerie')),
      body: photosAsync.when(
        data: (paginated) {
          if (paginated.items.isEmpty) {
            return const Center(child: Text("Cette galerie est vide pour le moment."));
          }
          return GridView.builder(
            padding: const EdgeInsets.all(8),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 3,
              crossAxisSpacing: 8,
              mainAxisSpacing: 8,
            ),
            itemCount: paginated.items.length,
            itemBuilder: (context, index) {
              final photo = paginated.items[index];
              return InkWell(
                onTap: () => context.push('/gallery/$galleryId/photo/${photo.remoteId}'),
                child: Hero(
                  tag: 'photo_${photo.remoteId}',
                  child: Image.network(
                    '${ApiConfig.baseUrl}/photos/${photo.remoteId}/storage',
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(
                      color: Colors.grey[300],
                      child: const Icon(Icons.broken_image),
                    ),
                  ),
                ),
              );
            },
          );
        },
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (err, _) => Center(child: Text('Erreur: $err')),
      ),
    );
  }
}
