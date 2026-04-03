// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'comment.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

_$CommentaireImpl _$$CommentaireImplFromJson(Map<String, dynamic> json) =>
    _$CommentaireImpl(
      id: json['id'] as String,
      photoId: json['photo_id'] as String,
      authorName: json['author_name'] as String,
      content: json['content'] as String,
      createdAt: DateTime.parse(json['created_at'] as String),
    );

Map<String, dynamic> _$$CommentaireImplToJson(_$CommentaireImpl instance) =>
    <String, dynamic>{
      'id': instance.id,
      'photo_id': instance.photoId,
      'author_name': instance.authorName,
      'content': instance.content,
      'created_at': instance.createdAt.toIso8601String(),
    };
