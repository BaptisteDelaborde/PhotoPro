import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:isar/isar.dart';
import 'package:path_provider/path_provider.dart';
import '../../data/models/galerie_model.dart';
import '../../data/models/photo_model.dart';

class PersistenceService {
  static late final Isar isar;
  static const secureStorage = FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
  );

  static Future<void> init() async {
    final dir = await getApplicationDocumentsDirectory();
    isar = await Isar.open(
      [GalerieModelSchema, PhotoModelSchema],
      directory: dir.path,
    );
  }
}
