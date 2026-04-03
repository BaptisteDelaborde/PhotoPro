import 'package:freezed_annotation/freezed_annotation.dart';

part 'error.freezed.dart';
part 'error.g.dart';

@freezed
class PhotoProError with _$PhotoProError {
  const factory PhotoProError({
    required String code,
    required String message,
    Map<String, dynamic>? details,
  }) = _PhotoProError;

  factory PhotoProError.fromJson(Map<String, dynamic> json) =>
      _$PhotoProErrorFromJson(json);
}
