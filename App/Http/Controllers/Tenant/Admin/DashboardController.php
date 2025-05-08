namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all folders for the current tenant
        $folders = Folder::where('tenant_id', tenant('id'))
            ->withCount('files')
            ->get();

        // Get unique categories
        $categories = $folders->pluck('category')->unique()->values()->all();

        return view('tenant.admin.dashboard', compact('folders', 'categories'));
    }
} 