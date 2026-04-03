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

  @JsonKey(name: 'url')
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
    // On copie le json pour éviter les erreurs d'immutabilité et on gère les champs potentiellement null
    final map = <String, dynamic>{...json};
    map['id'] = map['id']?.toString() ?? 'unknown_id_${DateTime.now().millisecondsSinceEpoch}';
    map['url'] = map['url']?.toString() ?? '';
    map['mime_type'] = map['mime_type']?.toString() ?? '';

    final fs = map['file_size'];
    if (fs is num) {
      map['file_size'] = fs.toDouble();
    } else if (fs is String) {
      map['file_size'] = double.tryParse(fs) ?? 0.0;
    } else {
      map['file_size'] = 0.0;
    }

    final model = _$PhotoModelFromJson(map);
    model.isarId = map['id'].hashCode;
    return model;
  }

  Map<String, dynamic> toJson() => _$PhotoModelToJson(this);
}
