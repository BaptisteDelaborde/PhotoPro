// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'error.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#adding-getters-and-methods-to-our-models');

PhotoProError _$PhotoProErrorFromJson(Map<String, dynamic> json) {
  return _PhotoProError.fromJson(json);
}

/// @nodoc
mixin _$PhotoProError {
  String get code => throw _privateConstructorUsedError;
  String get message => throw _privateConstructorUsedError;
  Map<String, dynamic>? get details => throw _privateConstructorUsedError;

  Map<String, dynamic> toJson() => throw _privateConstructorUsedError;
  @JsonKey(ignore: true)
  $PhotoProErrorCopyWith<PhotoProError> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $PhotoProErrorCopyWith<$Res> {
  factory $PhotoProErrorCopyWith(
          PhotoProError value, $Res Function(PhotoProError) then) =
      _$PhotoProErrorCopyWithImpl<$Res, PhotoProError>;
  @useResult
  $Res call({String code, String message, Map<String, dynamic>? details});
}

/// @nodoc
class _$PhotoProErrorCopyWithImpl<$Res, $Val extends PhotoProError>
    implements $PhotoProErrorCopyWith<$Res> {
  _$PhotoProErrorCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? code = null,
    Object? message = null,
    Object? details = freezed,
  }) {
    return _then(_value.copyWith(
      code: null == code
          ? _value.code
          : code // ignore: cast_nullable_to_non_nullable
              as String,
      message: null == message
          ? _value.message
          : message // ignore: cast_nullable_to_non_nullable
              as String,
      details: freezed == details
          ? _value.details
          : details // ignore: cast_nullable_to_non_nullable
              as Map<String, dynamic>?,
    ) as $Val);
  }
}

/// @nodoc
abstract class _$$PhotoProErrorImplCopyWith<$Res>
    implements $PhotoProErrorCopyWith<$Res> {
  factory _$$PhotoProErrorImplCopyWith(
          _$PhotoProErrorImpl value, $Res Function(_$PhotoProErrorImpl) then) =
      __$$PhotoProErrorImplCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call({String code, String message, Map<String, dynamic>? details});
}

/// @nodoc
class __$$PhotoProErrorImplCopyWithImpl<$Res>
    extends _$PhotoProErrorCopyWithImpl<$Res, _$PhotoProErrorImpl>
    implements _$$PhotoProErrorImplCopyWith<$Res> {
  __$$PhotoProErrorImplCopyWithImpl(
      _$PhotoProErrorImpl _value, $Res Function(_$PhotoProErrorImpl) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? code = null,
    Object? message = null,
    Object? details = freezed,
  }) {
    return _then(_$PhotoProErrorImpl(
      code: null == code
          ? _value.code
          : code // ignore: cast_nullable_to_non_nullable
              as String,
      message: null == message
          ? _value.message
          : message // ignore: cast_nullable_to_non_nullable
              as String,
      details: freezed == details
          ? _value._details
          : details // ignore: cast_nullable_to_non_nullable
              as Map<String, dynamic>?,
    ));
  }
}

/// @nodoc
@JsonSerializable()
class _$PhotoProErrorImpl implements _PhotoProError {
  const _$PhotoProErrorImpl(
      {required this.code,
      required this.message,
      final Map<String, dynamic>? details})
      : _details = details;

  factory _$PhotoProErrorImpl.fromJson(Map<String, dynamic> json) =>
      _$$PhotoProErrorImplFromJson(json);

  @override
  final String code;
  @override
  final String message;
  final Map<String, dynamic>? _details;
  @override
  Map<String, dynamic>? get details {
    final value = _details;
    if (value == null) return null;
    if (_details is EqualUnmodifiableMapView) return _details;
    // ignore: implicit_dynamic_type
    return EqualUnmodifiableMapView(value);
  }

  @override
  String toString() {
    return 'PhotoProError(code: $code, message: $message, details: $details)';
  }

  @override
  bool operator ==(Object other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$PhotoProErrorImpl &&
            (identical(other.code, code) || other.code == code) &&
            (identical(other.message, message) || other.message == message) &&
            const DeepCollectionEquality().equals(other._details, _details));
  }

  @JsonKey(ignore: true)
  @override
  int get hashCode => Object.hash(runtimeType, code, message,
      const DeepCollectionEquality().hash(_details));

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$PhotoProErrorImplCopyWith<_$PhotoProErrorImpl> get copyWith =>
      __$$PhotoProErrorImplCopyWithImpl<_$PhotoProErrorImpl>(this, _$identity);

  @override
  Map<String, dynamic> toJson() {
    return _$$PhotoProErrorImplToJson(
      this,
    );
  }
}

abstract class _PhotoProError implements PhotoProError {
  const factory _PhotoProError(
      {required final String code,
      required final String message,
      final Map<String, dynamic>? details}) = _$PhotoProErrorImpl;

  factory _PhotoProError.fromJson(Map<String, dynamic> json) =
      _$PhotoProErrorImpl.fromJson;

  @override
  String get code;
  @override
  String get message;
  @override
  Map<String, dynamic>? get details;
  @override
  @JsonKey(ignore: true)
  _$$PhotoProErrorImplCopyWith<_$PhotoProErrorImpl> get copyWith =>
      throw _privateConstructorUsedError;
}
