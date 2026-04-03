// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'comment.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#adding-getters-and-methods-to-our-models');

Commentaire _$CommentaireFromJson(Map<String, dynamic> json) {
  return _Commentaire.fromJson(json);
}

/// @nodoc
mixin _$Commentaire {
  String get id => throw _privateConstructorUsedError;
  @JsonKey(name: 'photo_id')
  String get photoId => throw _privateConstructorUsedError;
  @JsonKey(name: 'author_name')
  String get authorName => throw _privateConstructorUsedError;
  String get content => throw _privateConstructorUsedError;
  @JsonKey(name: 'created_at')
  DateTime get createdAt => throw _privateConstructorUsedError;

  Map<String, dynamic> toJson() => throw _privateConstructorUsedError;
  @JsonKey(ignore: true)
  $CommentaireCopyWith<Commentaire> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $CommentaireCopyWith<$Res> {
  factory $CommentaireCopyWith(
          Commentaire value, $Res Function(Commentaire) then) =
      _$CommentaireCopyWithImpl<$Res, Commentaire>;
  @useResult
  $Res call(
      {String id,
      @JsonKey(name: 'photo_id') String photoId,
      @JsonKey(name: 'author_name') String authorName,
      String content,
      @JsonKey(name: 'created_at') DateTime createdAt});
}

/// @nodoc
class _$CommentaireCopyWithImpl<$Res, $Val extends Commentaire>
    implements $CommentaireCopyWith<$Res> {
  _$CommentaireCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? id = null,
    Object? photoId = null,
    Object? authorName = null,
    Object? content = null,
    Object? createdAt = null,
  }) {
    return _then(_value.copyWith(
      id: null == id
          ? _value.id
          : id // ignore: cast_nullable_to_non_nullable
              as String,
      photoId: null == photoId
          ? _value.photoId
          : photoId // ignore: cast_nullable_to_non_nullable
              as String,
      authorName: null == authorName
          ? _value.authorName
          : authorName // ignore: cast_nullable_to_non_nullable
              as String,
      content: null == content
          ? _value.content
          : content // ignore: cast_nullable_to_non_nullable
              as String,
      createdAt: null == createdAt
          ? _value.createdAt
          : createdAt // ignore: cast_nullable_to_non_nullable
              as DateTime,
    ) as $Val);
  }
}

/// @nodoc
abstract class _$$CommentaireImplCopyWith<$Res>
    implements $CommentaireCopyWith<$Res> {
  factory _$$CommentaireImplCopyWith(
          _$CommentaireImpl value, $Res Function(_$CommentaireImpl) then) =
      __$$CommentaireImplCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call(
      {String id,
      @JsonKey(name: 'photo_id') String photoId,
      @JsonKey(name: 'author_name') String authorName,
      String content,
      @JsonKey(name: 'created_at') DateTime createdAt});
}

/// @nodoc
class __$$CommentaireImplCopyWithImpl<$Res>
    extends _$CommentaireCopyWithImpl<$Res, _$CommentaireImpl>
    implements _$$CommentaireImplCopyWith<$Res> {
  __$$CommentaireImplCopyWithImpl(
      _$CommentaireImpl _value, $Res Function(_$CommentaireImpl) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? id = null,
    Object? photoId = null,
    Object? authorName = null,
    Object? content = null,
    Object? createdAt = null,
  }) {
    return _then(_$CommentaireImpl(
      id: null == id
          ? _value.id
          : id // ignore: cast_nullable_to_non_nullable
              as String,
      photoId: null == photoId
          ? _value.photoId
          : photoId // ignore: cast_nullable_to_non_nullable
              as String,
      authorName: null == authorName
          ? _value.authorName
          : authorName // ignore: cast_nullable_to_non_nullable
              as String,
      content: null == content
          ? _value.content
          : content // ignore: cast_nullable_to_non_nullable
              as String,
      createdAt: null == createdAt
          ? _value.createdAt
          : createdAt // ignore: cast_nullable_to_non_nullable
              as DateTime,
    ));
  }
}

/// @nodoc
@JsonSerializable()
class _$CommentaireImpl implements _Commentaire {
  const _$CommentaireImpl(
      {required this.id,
      @JsonKey(name: 'photo_id') required this.photoId,
      @JsonKey(name: 'author_name') required this.authorName,
      required this.content,
      @JsonKey(name: 'created_at') required this.createdAt});

  factory _$CommentaireImpl.fromJson(Map<String, dynamic> json) =>
      _$$CommentaireImplFromJson(json);

  @override
  final String id;
  @override
  @JsonKey(name: 'photo_id')
  final String photoId;
  @override
  @JsonKey(name: 'author_name')
  final String authorName;
  @override
  final String content;
  @override
  @JsonKey(name: 'created_at')
  final DateTime createdAt;

  @override
  String toString() {
    return 'Commentaire(id: $id, photoId: $photoId, authorName: $authorName, content: $content, createdAt: $createdAt)';
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$CommentaireImpl &&
            (identical(other.id, id) || other.id == id) &&
            (identical(other.photoId, photoId) || other.photoId == photoId) &&
            (identical(other.authorName, authorName) ||
                other.authorName == authorName) &&
            (identical(other.content, content) || other.content == content) &&
            (identical(other.createdAt, createdAt) ||
                other.createdAt == createdAt));
  }

  @JsonKey(ignore: true)
  @override
  int get hashCode =>
      Object.hash(runtimeType, id, photoId, authorName, content, createdAt);

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$CommentaireImplCopyWith<_$CommentaireImpl> get copyWith =>
      __$$CommentaireImplCopyWithImpl<_$CommentaireImpl>(this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$$CommentaireImplToJson(
      this,
    );
  }
}

abstract class _Commentaire implements Commentaire {
  const factory _Commentaire(
          {required final String id,
          @JsonKey(name: 'photo_id') required final String photoId,
          @JsonKey(name: 'author_name') required final String authorName,
          required final String content,
          @JsonKey(name: 'created_at') required final DateTime createdAt}) =
      _$CommentaireImpl;

  factory _Commentaire.fromJson(Map<String, dynamic> json) =
      _$CommentaireImpl.fromJson;

  @override
  String get id;
  @override
  @JsonKey(name: 'photo_id')
  String get photoId;
  @override
  @JsonKey(name: 'author_name')
  String get authorName;
  @override
  String get content;
  @override
  @JsonKey(name: 'created_at')
  DateTime get createdAt;
  @override
  @JsonKey(ignore: true)
  _$$CommentaireImplCopyWith<_$CommentaireImpl> get copyWith =>
      throw _privateConstructorUsedError;
}
