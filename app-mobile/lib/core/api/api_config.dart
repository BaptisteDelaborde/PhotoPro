import 'dart:io' show Platform;
import 'package:flutter/foundation.dart' show kIsWeb;

class ApiConfig {
  static String get baseUrl {
    const envUrl = String.fromEnvironment('API_BASE_URL');
    if (envUrl.isNotEmpty) {
      return envUrl;
    }
    if (kIsWeb) {
      return 'http://localhost:8082';
    } else if (Platform.isAndroid) {
      return 'http://10.0.2.2:8082';
    } else {
      return 'http://localhost:8082';
    }
  }

  static const Duration timeout = Duration(seconds: 10);
}
