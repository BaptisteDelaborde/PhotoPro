import 'package:isar/isar.dart';
import 'package:json_annotation/json_annotation.dart';

part 'photo_model.g.dart';

@collection
@JsonSerializable()
class PhotoModel {
  Id? isarId;

  @JsonKey(name: 'id')
  @Index(unique: true, replace: true)
  final String remoteId;

  final String? title;

  @JsonKey(name: 'storage_url')
  final String storageUrl;

  @JsonKey(name: 'mime_type')
  final String mimeType;

  @JsonKey(name: 'file_size')
  final double fileSize;

  PhotoModel({
    this.isarId,
    required this.remoteId,
    this.title,
    required this.storageUrl,
    required this.mimeType,
    required this.fileSize,
  });

  factory PhotoModel.fromJson(Map<String, dynamic> json) {
    final model = _$PhotoModelFromJson(json);
    model.isarId = json['id'].hashCode;
    return model;
  }

  Map<String, dynamic> toJson() => _$PhotoModelToJson(this);
}
