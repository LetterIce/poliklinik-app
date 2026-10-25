<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periksa extends Model
{
    protected $table = 'periksa';

    protected $fillable = [
        'id_daftar_poli',
        'tgl_periksa',
        'catatan',
        'biaya_dokter',
        'biaya_periksa'
    ];

    public function getBiayaDokterAttribute($value)
    {
        if ($value !== null) {
            return (int) $value;
        }
        return max(0, (int) $this->biaya_periksa - $this->total_biaya_obat);
    }

    public function getTotalBiayaObatAttribute()
    {
        if ($this->relationLoaded('detailPeriksas')) {
            return (int) $this->detailPeriksas->sum(fn($dp) => $dp->obat->harga ?? 0);
        }
        return (int) $this->detailPeriksas()->with('obat')->get()->sum(fn($dp) => $dp->obat->harga ?? 0);
    }

    public function daftarPoli()
    {
        return $this->belongsTo(DaftarPoli::class, 'id_daftar_poli');
    }

    public function detailPeriksas()
    {
        return $this->hasMany(DetailPeriksa::class, 'id_periksa');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_periksa');
    }
}