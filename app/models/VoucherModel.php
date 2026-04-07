<?php
// app/models/VoucherModel.php

class VoucherModel extends Model
{
    protected string $table = 'vouchers';

    public function findByCode(string $code): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM vouchers WHERE code=:code AND is_active=1 LIMIT 1");
        $stmt->execute([':code'=>strtoupper(trim($code))]);
        return $stmt->fetch();
    }

    public function validate(string $code, float $subtotal): array
    {
        $voucher = $this->findByCode($code);
        if (!$voucher) return ['valid'=>false,'message'=>'Kode voucher tidak ditemukan.'];
        if ($voucher['used_count'] >= $voucher['quota']) return ['valid'=>false,'message'=>'Kuota voucher sudah habis.'];
        if (date('Y-m-d') < $voucher['valid_from']) return ['valid'=>false,'message'=>'Voucher belum aktif.'];
        if (date('Y-m-d') > $voucher['valid_until']) return ['valid'=>false,'message'=>'Voucher sudah kadaluarsa.'];
        if ($subtotal < $voucher['min_purchase']) {
            return ['valid'=>false,'message'=>'Minimum pembelian ' . formatRupiah($voucher['min_purchase']) . ' untuk voucher ini.'];
        }

        $discount = 0;
        if ($voucher['type'] === 'percentage') {
            $discount = $subtotal * ($voucher['value'] / 100);
            if ($voucher['max_discount']) $discount = min($discount, $voucher['max_discount']);
        } else {
            $discount = min($voucher['value'], $subtotal);
        }

        return ['valid'=>true, 'voucher'=>$voucher, 'discount'=>$discount, 'message'=>'Voucher berhasil diterapkan!'];
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM vouchers ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO vouchers (code,type,value,min_purchase,max_discount,quota,valid_from,valid_until,description)
             VALUES (:code,:type,:val,:min,:max,:quota,:from,:until,:desc)"
        );
        $stmt->execute([
            ':code'=>strtoupper($data['code']), ':type'=>$data['type'],
            ':val'=>$data['value'],             ':min'=>$data['min_purchase'],
            ':max'=>$data['max_discount']?:null,':quota'=>$data['quota'],
            ':from'=>$data['valid_from'],       ':until'=>$data['valid_until'],
            ':desc'=>$data['description'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE vouchers SET code=:code, type=:type, value=:val, min_purchase=:min,
             max_discount=:max, quota=:quota, valid_from=:from, valid_until=:until,
             description=:desc, is_active=:active WHERE id=:id"
        );
        return $stmt->execute([
            ':code'=>strtoupper($data['code']), ':type'=>$data['type'],
            ':val'=>$data['value'],             ':min'=>$data['min_purchase'],
            ':max'=>$data['max_discount']?:null,':quota'=>$data['quota'],
            ':from'=>$data['valid_from'],       ':until'=>$data['valid_until'],
            ':desc'=>$data['description'],      ':active'=>$data['is_active']??1,
            ':id'=>$id,
        ]);
    }
}
