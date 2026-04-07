import 'dart:io' show Platform;
import 'package:flutter/foundation.dart' show kIsWeb;

class ApiConfig {
  static String get baseUrl {
    const envUrl = String.fromEnvironment('API_BASE_URL');
    if (envUrl.isNotEmpty) {
      return envUrl;
    }
    return 'http://docketu.iutnc.univ-lorraine.fr:21856';
  }

  static const Duration timeout = Duration(seconds: 10);
}
