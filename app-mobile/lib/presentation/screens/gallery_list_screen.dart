import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../providers/gallery_providers.dart';
import '../../data/models/galerie_model.dart';
import '../../core/api/api_config.dart';

class GalleryListScreen extends ConsumerWidget {
  const GalleryListScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final galleriesAsync = ref.watch(publicGalleriesProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('PhotoPro', style: TextStyle(fontWeight: FontWeight.bold)),
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.vpn_key_outlined),
            onPressed: () => context.push('/access-code'),
            tooltip: 'Accès code',
          ),
        ],
      ),
      body: galleriesAsync.when(
        data: (paginated) => RefreshIndicator(
          onRefresh: () => ref.refresh(publicGalleriesProvider.future),
          child: Column(
            children: [
              const Padding(
                padding: EdgeInsets.symmetric(vertical: 8.0),
                child: Text('Exploration des galeries publiques', style: TextStyle(color: Colors.grey)),
              ),
              Expanded(
                child: ListView.builder(
                  itemCount: paginated.items.length,
                  itemBuilder: (context, index) {
                    final gallery = paginated.items[index];
                    return GalleryCard(gallery: gallery);
                  },
                ),
              ),
            ],
          ),
        ),
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (err, stack) => Center(
          child: Padding(
            padding: const EdgeInsets.all(32.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.wifi_off, size: 64, color: Colors.orange),
                const SizedBox(height: 16),
                const Text('Impossible de se connecter', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                const SizedBox(height: 8),
                Text(err.toString(), textAlign: TextAlign.center, style: const TextStyle(color: Colors.grey)),
                const SizedBox(height: 24),
                ElevatedButton.icon(
                  onPressed: () => ref.refresh(publicGalleriesProvider),
                  icon: const Icon(Icons.refresh),
                  label: const Text('Réessayer'),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class GalleryCard extends StatelessWidget {
  final GalerieModel gallery;
  const GalleryCard({super.key, required this.gallery});

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      elevation: 4,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => context.push('/gallery/${gallery.remoteId}'),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            AspectRatio(
              aspectRatio: 16 / 9,
              child: gallery.coverPhotoId != null
                  ? Image.network(
                      '${ApiConfig.baseUrl}/photos/${gallery.coverPhotoId}/storage',
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => Container(
                        color: Colors.blueGrey[50],
                        child: const Icon(Icons.image_outlined, size: 40, color: Colors.blueGrey),
                      ),
                    )
                  : Container(
                      decoration: const BoxDecoration(
                        gradient: LinearGradient(
                          colors: [Colors.blueGrey, Colors.grey],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                      ),
                      child: const Icon(Icons.photo_library_outlined, size: 48, color: Colors.white70),
                    ),
            ),
            Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          gallery.title,
                          style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                        ),
                      ),
                      const Icon(Icons.arrow_forward_ios, size: 16, color: Colors.grey),
                    ],
                  ),
                  if (gallery.description != null) ...[
                    const SizedBox(height: 8),
                    Text(
                      gallery.description!,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(color: Colors.grey[700]),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
