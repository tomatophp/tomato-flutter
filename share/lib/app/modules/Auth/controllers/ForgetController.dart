import 'package:get/get.dart';

import '../../../shared/controllers/AppController.dart';

class ForgetController extends AppController {
  /// Create and get the instance of the controller
  static ForgetController get instance {
    if (!Get.isRegistered<ForgetController>()) Get.put(ForgetController());
    return Get.find<ForgetController>();
  }
  
  /// Observables
  var _exampleBool = false.obs;

  /// Getters
  bool get exampleBool => _exampleBool.value;

  @override
  void onInit() {
    super.onInit();
    /// Do something here
  }
  
  void exampleMethod() {
    // TODO: implement exampleMethod
    throw UnimplementedError();
  }
}
