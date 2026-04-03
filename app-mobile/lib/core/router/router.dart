import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../presentation/screens/gallery_list_screen.dart';
import '../../presentation/screens/access_code_screen.dart';
import '../../presentation/screens/gallery_detail_screen.dart';
import '../../presentation/screens/photo_detail_screen.dart';

final GlobalKey<NavigatorState> _rootNavigatorKey = GlobalKey<NavigatorState>();

final routerConfig = GoRouter(
  navigatorKey: _rootNavigatorKey,
  initialLocation: '/',
  routes: [
    GoRoute(
      path: '/',
      builder: (context, state) => const GalleryListScreen(),
    ),
    GoRoute(
      path: '/access-code',
      builder: (context, state) => const AccessCodeScreen(),
    ),
    GoRoute(
      path: '/gallery/:gid',
      builder: (context, state) {
        final gid = state.pathParameters['gid']!;
        return GalleryDetailScreen(galleryId: gid);
      },
    ),
    GoRoute(
      path: '/gallery/:gid/photo/:pid',
      builder: (context, state) {
        final gid = state.pathParameters['gid']!;
        final pid = state.pathParameters['pid']!;
        return PhotoDetailScreen(galleryId: gid, photoId: pid);
      },
    ),
  ],
  errorBuilder: (context, state) => Scaffold(
    appBar: AppBar(title: const Text('Erreur')),
    body: Center(
      child: Text('Page non trouvée : ${state.uri}'),
    ),
  ),
);
