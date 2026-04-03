import 'package:isar/isar.dart';
import 'package:json_annotation/json_annotation.dart';

part 'galerie_model.g.dart';

@collection
@JsonSerializable()
class GalerieModel {
  Id? isarId;

  @JsonKey(name: 'id')
  @Index(unique: true, replace: true)
  final String remoteId;

  @JsonKey(name: 'photographer_id')
  final String photographerId;

  final String title;
  final String? description;

  @JsonKey(name: 'cover_photo_id')
  final String? coverPhotoId;

  @JsonKey(name: 'is_public')
  final bool isPublic;

  final String layout;

  @JsonKey(name: 'access_code')
  final String? accessCode;

  @JsonKey(name: 'created_at')
  final DateTime createdAt;

  GalerieModel({
    this.isarId,
    required this.remoteId,
    required this.photographerId,
    required this.title,
    this.description,
    this.coverPhotoId,
    required this.isPublic,
    required this.layout,
    this.accessCode,
    required this.createdAt,
  });

  factory GalerieModel.fromJson(Map<String, dynamic> json) {
    final model = _$GalerieModelFromJson(json);
    model.isarId = json['id'].hashCode;
    return model;
  }

  Map<String, dynamic> toJson() => _$GalerieModelToJson(this);
}
