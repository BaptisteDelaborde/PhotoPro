import 'package:flutter_riverpod/flutter_riverpod.dart';

class ErrorState {
  final String? message;
  ErrorState(this.message);
}

final errorProvider = StateProvider<ErrorState?>((ref) => null);
