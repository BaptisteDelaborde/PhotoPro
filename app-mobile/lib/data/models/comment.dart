import 'package:freezed_annotation/freezed_annotation.dart';

part 'comment.freezed.dart';
part 'comment.g.dart';

@freezed
class Commentaire with _$Commentaire {
  const factory Commentaire({
    required String id,
    @JsonKey(name: 'photo_id') required String photoId,
    @JsonKey(name: 'author_name') required String authorName,
    required String content,
    @JsonKey(name: 'created_at') required DateTime createdAt,
  }) = _Commentaire;

  factory Commentaire.fromJson(Map<String, dynamic> json) =>
      _$CommentaireFromJson(json);
}
