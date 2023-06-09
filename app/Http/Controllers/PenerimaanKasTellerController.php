<?php

namespace App\Http\Controllers;

use App\Models\Denominasi;
use App\Models\SaldoTeller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenerimaanKasTellerController extends Controller
{
    public function index()
    {
        $currentDate = Carbon::now()->toDateString();
        $pembayaran = SaldoTeller::where('status','pembayaran')
                                ->where('id_user',auth()->user()->id)
                                ->where('tanggal',$currentDate)
                                // ->sum('pembayaran');
                                ->first();
        $penerimaan = SaldoTeller::where('penerimaan','!=',0)
                    ->where('id_user',auth()->user()->id)
                    ->where('tanggal',$currentDate)
                    // ->sum('pembayaran');
                    ->count();
        return gettype($penerimaan);

        $denominasi = Denominasi::where('id_user',auth()->user()->id)->whereDate('created_at','=',$currentDate)->groupBy('id_user')->get();
        return view('pages.penerimaan.index',compact('pembayaran','penerimaan','denominasi'));
    }

    public function post(Request $request)
    {

        $request->validate([
            'nominal.*' => 'required',
            'jumlah.*' => 'required'
            ]);

        try {
            $currentDate = Carbon::now()->toDateString();
            $current_penerimaan = SaldoTeller::where('status','pembayaran')
                                    ->where('id_user',auth()->user()->id)
                                    ->where('tanggal',$currentDate)
                                    ->first()->penerimaan;
            $denominasi = (int) $request->get('penerimaan_total');
            if ($current_penerimaan != $denominasi) {
                return redirect()->back()->withError('Data tidak sesuai.');
            }
            for ($i=0; $i < count($request->get('nominal')); $i++) {
                $denominasi = new Denominasi;
                $denominasi->id_user = auth()->user()->id;
                $denominasi->nominal = (int) $request->get('nominal')[$i];
                $denominasi->jumlah = (int) $request->get('jumlah')[$i];
                $denominasi->total = (int) $request->get('jumlah')[$i] * (int) $request->get('nominal')[$i];
                $denominasi->save();
            }
            return redirect()->route('penerimaan.kas-teller')->withStatus('Berhasil menambahkan data dengan nominal'.$request->get('penerimaan_total'));
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (QueryException $e){
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }
    public function formatNumber($param)
    {
        return (int)str_replace('.', '', $param);
    }
}
