<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'id_periksa',
        'id_pasien',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_bayar',
        'status',
        'tgl_bayar',
        'tgl_verifikasi',
        'verified_by',
        'catatan_admin'
    ];

    protected $casts = [
        'tgl_bayar' => 'datetime',
        'tgl_verifikasi' => 'datetime',
    ];

    public function periksa()
    {
        return $this->belongsTo(Periksa::class, 'id_periksa');
    }

    public function pasien()
    {
        return $this->belongsTo(User::class, 'id_pasien');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
