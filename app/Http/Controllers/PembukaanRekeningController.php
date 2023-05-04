<?php

namespace App\Http\Controllers;

use App\Models\BukuTabungan;
use App\Models\KodeAkun;
use App\Models\NasabahModel;
use App\Models\PembukaanRekening;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembukaanRekeningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PembukaanRekening::with('nasabah')->get();
        $kode = KodeAkun::where('nama_akun', 'LIKE', 'tabungan%')->get();
        $nasabah = NasabahModel::all();
        $date = date('Yd');

        /* generate no anggota  */
        $noAnggota = null;
        $rekening = PembukaanRekening::orderBy('created_at', 'DESC')->get();

        if($rekening->count() > 0) {
            $noRekening = $rekening[0]->no_rekening;

            $lastIncrement = substr($noRekening, 6);

            $noRekening = str_pad($lastIncrement + 1, 4, 0, STR_PAD_LEFT);
            $noRekening = $date.$noRekening;
        }
        else {
            $noRekening = $date."0001";

        }
        return view('pages.pembukaan-rekening.index',compact('data','nasabah','noRekening','kode'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $count = PembukaanRekening::count();
        if ($count > 0) {
            $nasabah = PembukaanRekening::where('nasabah_id',$request->get('id_nasabah'))->first();
            $isUniquenasabah = ($nasabah != null) ? $isUniquenasabah = $nasabah->nasabah_id != $request->id_nasabah ? '' : '|unique:rekening_tabungan,nasabah_id' : '' ;
        }else{
            $isUniquenasabah = '';
        }
        $request->validate([
            'id_nasabah' => 'required'.$isUniquenasabah,
            'tgl' => 'required',
            'no_rekening' => 'required',
        ],[
            'unique' => 'Data sudah tersedia.'
        ]);

        try {
            $nasabah = NasabahModel::find($request->get('id_nasabah'));
            $rekening = new PembukaanRekening;
            $rekening->no_rekening = $request->get('no_rekening');
            $rekening->id_kode_akun = $request->get('kode');
            $rekening->tgl = $nasabah->tgl;
            $rekening->tgl_transaksi = $request->get('tgl');
            $rekening->saldo_awal = $this->formatNumber($request->saldo_awal);
            $rekening->ket = $request->get('ket');
            $rekening->nasabah_id = $request->get('id_nasabah');
            $rekening->save();

            $buku  = new BukuTabungan;
            $buku->id_rekening_tabungan = $rekening->id;
            $buku->tgl_transaksi = $request->get('tgl');
            $buku->nominal_transaksi = $rekening->saldo_awal;
            $buku->saldo = $rekening->saldo_awal;
            $buku->jenis = 'masuk';
            $buku->save();
            return redirect()->route('pembukaan-rekening.index')->withStatus('Berhasil menambahkan data.');
        } catch (Exception $e) {
            return redirect()->route('pembukaan-rekening.index')->withError('Terjadi kesalahan.');
        } catch (QueryException $e){
            return redirect()->route('pembukaan-rekening.index')->withError('Terjadi kesalahan.');
        }
    }

    public function cetak($id)
    {
        $data = PembukaanRekening::with('nasabah')->find($id);
        return view('pages.pembukaan-rekening.cetak',compact('data'));
    }

    public function formatNumber($param)
    {
        return (int)str_replace('.', '', $param);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = PembukaanRekening::with('nasabah')->find($id);
        return view('pages.pembukaan-rekening.show',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            PembukaanRekening::findOrFail($id)->delete();
            return redirect()->route('pembukaan-rekening.index')->withStatus('Berhasil Menghapus data.');
        } catch (Exception $e) {
            return redirect()->route('pembukaan-rekening.index')->withError('Terjadi kesalahan.');
        } catch (QueryException $e){
            return redirect()->route('pembukaan-rekening.index')->withError('Terjadi kesalahan.');
        }
    }
}
