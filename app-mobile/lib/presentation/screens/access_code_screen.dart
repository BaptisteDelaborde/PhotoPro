import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../providers/gallery_providers.dart';

class AccessCodeScreen extends ConsumerStatefulWidget {
  const AccessCodeScreen({super.key});

  @override
  ConsumerState<AccessCodeScreen> createState() => _AccessCodeScreenState();
}

class _AccessCodeScreenState extends ConsumerState<AccessCodeScreen> {
  final _codeController = TextEditingController();
  bool _isLoading = false;
  String? _errorMessage;

  @override
  void dispose() {
    _codeController.dispose();
    super.dispose();
  }

  Future<void> _validateCode() async {
    final code = _codeController.text.trim();
    if (code.isEmpty) {
      setState(() => _errorMessage = 'Le code ne peut pas être vide.');
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final repository = ref.read(galleryRepositoryProvider);
      final gallery = await repository.getGalleryByCode(code);
      
      if (mounted) {
        // Navigate to gallery detail
        context.push('/gallery/${gallery.remoteId}');
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _errorMessage = 'Code invalide ou galerie introuvable.';
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Accès Privé')),
      body: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.lock_person, size: 80, color: Colors.blueGrey),
            const SizedBox(height: 24),
            const Text(
              'Veuillez entrer le code d\'accès fourni par votre photographe.',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 16),
            ),
            const SizedBox(height: 32),
            TextField(
              controller: _codeController,
              decoration: InputDecoration(
                labelText: 'Code secret',
                border: const OutlineInputBorder(),
                errorText: _errorMessage,
                prefixIcon: const Icon(Icons.key),
              ),
              onSubmitted: (_) => _validateCode(),
            ),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _validateCode,
                child: _isLoading
                    ? const CircularProgressIndicator()
                    : const Text('Valider'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
