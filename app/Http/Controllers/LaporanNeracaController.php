<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\KodeAkun;
use App\Models\KodeInduk;
use App\Models\KodeLedger;
use App\Models\TransaksiManyToMany;
use Illuminate\Http\Request;

class LaporanNeracaController extends Controller
{
    public function neraca(Request $request){
        // $pencarian = KodeAkun::select('kode_akun.*',
        //             'kode_induk.id as kode_induk_id',
        //             'kode_induk.kode_induk as nama_kode')

        //             ->join('kode_induk','kode_induk.id','kode_akun.id_induk');
        // if ($request->has('nama_akun') && $request->get('nama_akun') != null ) {
        //     $kata_kunci_nama_akun = strtolower($request->get('nama_akun'));
        //     $pencarian = $pencarian
        //                 ->where('kode_akun.nama_akun','LIKE', "%".$kata_kunci_nama_akun."%")
        //                 ->get();
        //     return $pencarian;

        // }
        $kode_pendapatan = KodeAkun::select('kode_akun.id',
                                'kode_induk.id as induk_id',
                                'kode_induk.kode_induk',
                                'kode_induk.nama as nama_induk','kode_induk.jenis',
                                'kode_ledger.id as ledger_id','kode_ledger.kode_ledger','kode_ledger.nama as nama_ledger')
                        ->join('kode_induk','kode_induk.id','kode_akun.id_induk')
                        ->join('kode_ledger','kode_ledger.id','kode_induk.id_ledger')
                        ->where('kode_ledger.kode_ledger','30000')
                        ->get();
        $kode_modal = KodeAkun::select('kode_akun.id',
                                'kode_induk.id as induk_id',
                                'kode_induk.kode_induk',
                                'kode_induk.nama as nama_induk','kode_induk.jenis',
                                'kode_ledger.id as ledger_id','kode_ledger.kode_ledger','kode_ledger.nama as nama_ledger')
                                ->join('kode_induk','kode_induk.id','kode_akun.id_induk')
                                ->join('kode_ledger','kode_ledger.id','kode_induk.id_ledger')
                                ->where('kode_ledger.kode_ledger','40000')
                                ->get();
        $kode_induk = KodeInduk::select('kode_induk.*','kode_ledger.id as ledger_id','kode_ledger.kode_ledger','kode_ledger.nama as nama_ledger')
                                ->join('kode_ledger','kode_ledger.id','kode_induk.id_ledger')
                                ->groupBy('kode_ledger.nama')
                                ->orderBy('kode_induk.kode_induk')
                                ->get();
        $KodeAkun = KodeAkun::select('kode_akun.*',
                                'kode_induk.id as kode_induk_id',
                                'kode_induk.kode_induk as nama_kode')
                                ->join('kode_induk','kode_induk.id','kode_akun.id_induk')
                                ->where('kode_akun.nama_akun','NOT LIKE', "%tabungan mudharabah%")
                                ->get();
        return view('pages.laporan.neraca.index',compact('kode_induk','KodeAkun','kode_pendapatan','kode_modal'));
    }

    public function cetak(){

        $kode_pendapatan = KodeAkun::select('kode_akun.id',
                                'kode_induk.id as induk_id',
                                'kode_induk.kode_induk',
                                'kode_induk.nama as nama_induk','kode_induk.jenis',
                                'kode_ledger.id as ledger_id','kode_ledger.kode_ledger','kode_ledger.nama as nama_ledger')
                        ->join('kode_induk','kode_induk.id','kode_akun.id_induk')
                        ->join('kode_ledger','kode_ledger.id','kode_induk.id_ledger')
                        ->where('kode_ledger.kode_ledger','30000')
                        ->get();
        $kode_modal = KodeAkun::select('kode_akun.id',
                                'kode_induk.id as induk_id',
                                'kode_induk.kode_induk',
                                'kode_induk.nama as nama_induk','kode_induk.jenis',
                                'kode_ledger.id as ledger_id','kode_ledger.kode_ledger','kode_ledger.nama as nama_ledger')
                                ->join('kode_induk','kode_induk.id','kode_akun.id_induk')
                                ->join('kode_ledger','kode_ledger.id','kode_induk.id_ledger')
                                ->where('kode_ledger.kode_ledger','40000')
                                ->get();
        $kode_induk = KodeInduk::select('kode_induk.*','kode_ledger.id as ledger_id','kode_ledger.kode_ledger','kode_ledger.nama as nama_ledger')
                                ->join('kode_ledger','kode_ledger.id','kode_induk.id_ledger')
                                ->groupBy('kode_ledger.nama')

                                ->orderBy('kode_induk.kode_induk')
                                ->get();
        $KodeAkun = KodeAkun::select('kode_akun.*',
                                'kode_induk.id as kode_induk_id',
                                'kode_induk.kode_induk as nama_kode')
                                ->join('kode_induk','kode_induk.id','kode_akun.id_induk')
                                ->where('kode_akun.nama_akun','NOT LIKE', "%tabungan mudharabah%")
                                ->get();
        return view('pages.laporan.neraca.pdf',compact('kode_induk','KodeAkun','kode_pendapatan','kode_modal'));
    }
}
