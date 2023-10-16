import '/app/models/ApiResponse.dart';
import '/app/shared/services/Services.dart';
import 'DashboardService.dart';

class MockDashboardService extends BaseService implements DashboardService {
  @override
  Future<ApiResponse> doSomething({required String client}) async {
    // TODO: implement googleLogin
    throw UnimplementedError();
  }
}
