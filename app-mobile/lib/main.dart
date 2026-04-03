import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/router/router.dart';
import 'core/persistence/persistence_service.dart';
import 'presentation/providers/error_provider.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await PersistenceService.init();
  
  runApp(
    const ProviderScope(
      child: PhotoProApp(),
    ),
  );
}

class PhotoProApp extends ConsumerWidget {
  const PhotoProApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return MaterialApp.router(
      title: 'PhotoPro',
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.blueGrey),
      ),
      routerConfig: routerConfig,
      debugShowCheckedModeBanner: false,
      builder: (context, child) {
        return ScaffoldMessenger(
          child: GlobalErrorListener(child: child!),
        );
      },
    );
  }
}

class GlobalErrorListener extends ConsumerWidget {
  final Widget child;
  const GlobalErrorListener({super.key, required this.child});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    ref.listen<ErrorState?>(errorProvider, (previous, next) {
      if (next?.message != null) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(next!.message!),
            backgroundColor: Colors.red,
            behavior: SnackBarBehavior.floating,
          ),
        );
        // Reset error after showing
        ref.read(errorProvider.notifier).state = null;
      }
    });
    return child;
  }
}
