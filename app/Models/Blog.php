<?php

namespace App\Models;

use CodeIgniter\Model;

class Blog extends Model
{
    protected $table            = 'blogs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['title', 'slug', 'content', 'author_id', 'image'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]',
        'image' => 'permit_empty|max_size[image,2048]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function generateSlug($title)
    {
        $slug = url_title($title, '-', TRUE);

        $random = bin2hex(random_bytes(5));
        $slug = $slug . '-' . $random;

        return $slug;
    }

    public function getBlogsWithAuthorDetails($id = null, $search = null, $authorId = null)
    {
        $builder = $this->db->table('blogs');
        $builder->select('blogs.*, users.name as author_name');
        $builder->join('users', 'users.id = blogs.author_id');

        if ($id) {
            $builder->where('blogs.id', $id);
            return $builder->get()->getRowArray();
        }

        if ($search) {
            $builder->like('title', $search);
            $builder->orLike('slug', $search);
            $builder->orLike('content', $search);
            return $builder->get()->getResultArray();
        }

        if ($authorId) {
            $builder->where('blogs.author_id', $authorId);
            return $builder->get()->getResultArray();
        }

        $builder->orderBy('blogs.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }
}
