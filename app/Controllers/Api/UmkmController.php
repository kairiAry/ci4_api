<?php

namespace App\Controllers\Api;

use App\Models\UmkmModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class UmkmController extends ResourceController
{
    // a. Get All List UMKM (GET /api/umkm)
    public function index(): ResponseInterface
    {
        $umkmModel = new UmkmModel();
        $umkm = $umkmModel->findAll();

        return $this->respond([
            'success' => true,
            'message' => 'Data UMKM berhasil diambil',
            'data'    => $umkm,
        ], 200);
    }

    // b. Create UMKM (POST /api/umkm)
    public function create(): ResponseInterface
    {
        $validation = \Config\Services::validation();

        $rules = [
            'nama_umkm' => 'required|min_length[3]|is_unique[umkm.nama_umkm]',
            'kategori'  => 'permit_empty|string',
            'pemilik'   => 'permit_empty|string',
            'alamat'    => 'permit_empty|string',
            'kontak'    => 'permit_empty|string',
        ];

        $data = $this->request->getPost();

        if (!$validation->setRules($rules)->run($data)) {
            return $this->respond([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validation->getErrors(),
            ], 400);
        }

        $umkmModel = new UmkmModel();
        $insertedId = $umkmModel->insert($data);

        if ($insertedId === false) {
            return $this->respond([
                'success' => false,
                'message' => 'Gagal menambahkan data UMKM',
                'errors'  => $umkmModel->errors(),
            ], 500);
        }

        $newData = $umkmModel->find($insertedId);

        return $this->respondCreated([
            'success' => true,
            'message' => 'Data UMKM berhasil ditambahkan',
            'data'    => $newData,
        ]);
    }

    // c. Update UMKM (PUT /api/umkm/{id})
    public function update($id = null): ResponseInterface
    {
        $data = $this->request->getRawInput();
        $umkmModel = new UmkmModel();
        $umkm = $umkmModel->update($id, $data);

        return $this->respond([
            'success' => true,
            'message' => 'Data UMKM berhasil diubah',
            'data'    => $umkm,
        ], 200);
    }

    // d. Delete UMKM (DELETE /api/umkm/{id})
    public function delete($id = null): ResponseInterface
    {
        $umkmModel = new UmkmModel();
        $data = $umkmModel->find($id);

        if (!$data) {
            return $this->respond([
                'success' => false,
                'message' => 'Data UMKM tidak ditemukan',
            ], 404);
        }

        $umkmModel->delete($id);

        return $this->respond([
            'success' => true,
            'message' => 'Data UMKM berhasil dihapus',
            'data'    => $data,
        ], 200);
    }
}
