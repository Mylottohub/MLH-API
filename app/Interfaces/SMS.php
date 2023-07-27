namespace App\Interfaces;

use Illuminate\Http\Request;

interface SMS
{
    public function send(Request $request);

    public function getLogs(Request $request);

  
}