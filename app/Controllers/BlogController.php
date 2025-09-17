<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Blog;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class BlogController extends BaseController
{
    use ResponseTrait;
    protected $blogModel;
    protected $helpers = ['url', 'form'];

    public function __construct()
    {
        $this->blogModel = new Blog();
    }

    public function index()
    {
        $blogs = $this->blogModel->getBlogsWithAuthorDetails();

        return $this->respond([
            'status' => 200,
            'blogs' => $blogs
        ]);
    }

    public function search()
    {
        $search = $this->request->getGet('search');
        $blogs = $this->blogModel->getBlogsWithAuthorDetails(search: $search);
        return $this->respond([
            'status' => 200,
            'blogs' => $blogs
        ]);
    }

    public function getRandomBlogs()
    {
        $blogs = $this->blogModel->orderBy('id', 'RANDOM')->limit(5)->findAll();
        return $this->respond([
            'status' => 200,
            'blogs' => $blogs
        ]);
    }

    public function getBlogsByAuthor($authorId)
    {
        $blogs = $this->blogModel->getBlogsWithAuthorDetails(authorId: $authorId);
        return $this->respond([
            'status' => 200,
            'blogs' => $blogs
        ]);
    }

    public function show($id)
    {
        $blog = $this->blogModel->getBlogsWithAuthorDetails(id: $id);

        if (!$blog) {
            return $this->failNotFound('Blog not found');
        }

        return $this->respond([
            'status' => 200,
            'blog' => $blog
        ]);
    }

    public function showBySlug($slug)
    {
        $blog = $this->blogModel->where('slug', $slug)->first();

        if (!$blog) {
            return $this->failNotFound('Blog not found');
        }

        return $this->respond([
            'status' => 200,
            'blog' => $blog
        ]);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|min_length[10]',
            'image' => 'uploaded[image]|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $imageFile = $this->request->getFile('image');
        $imageName = null;

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newImageName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads/blogs', $newImageName);
            $imageName = $newImageName;
        }

        $title = $this->request->getPost('title');
        $slug = $this->blogModel->generateSlug($title);

        $data = [
            'title' =>$title,
            'slug' =>$slug,
            'content' => $this->request->getPost('content'),
            'image' => $imageName,
            'author_id' => $this->request->user['id']
        ];

        if ($this->blogModel->save($data)) {
            $blogId = $this->blogModel->getInsertID();
            $blog = $this->blogModel->getBlogsWithAuthorDetails(id: $blogId);

            return $this->respond([
                'status' => 201,
                'message' => 'Blog created successfully',
                'blog' => $blog
            ]);
        } else {
            return $this->failValidationErrors($this->blogModel->errors());
        }
    }

    public function update($id)
    {
        $blog = $this->blogModel->find($id);

        if (!$blog) {
            return $this->failNotFound('Blog not found');
        }

        if ($blog['author_id'] != $this->request->user['id']){
            return $this->failforbidden('You are not allowed to edit this blog');
        }

        $rules = [
            'title' => 'permit_empty|min_length[3]|max_length[255]',
            'content' => 'permit_empty|min_length[10]',
            'image' => 'if_exist|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [];

        if ($this->request->getPost('title')) {
            $data['title'] = $this->request->getPost('title');
        }
        if ($this->request->getPost('content')) {
            $data['content'] = $this->request->getPost('content');
        }

        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            if ($blog['image']) {
                if (file_exists(ROOTPATH . 'public/uploads/blogs/' . $blog['image'])) {
                    unlink(ROOTPATH . 'public/uploads/blogs/' . $blog['image']);
                }
            }
            $newImageName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads/blogs', $newImageName);
            $data['image'] = $newImageName;
        }

        if ($this->blogModel->update($id, $data)) {
            $blog = $this->blogModel->getBlogsWithAuthorDetails(id: $id);
            return $this->respond([
                'status' => 200,
                'message' => 'Blog updated successfully',
                'blog' => $blog
            ]);
        }
        return $this->failServerError('Something went wrong');
    }

    public function destroy($id)
    {
        $blog = $this->blogModel->find($id);
        if (!$blog) {
            return $this->failNotFound('Blog not found');
        }

        if ($blog['image']) {
            if (file_exists(ROOTPATH . 'public/uploads/blogs/' . $blog['image'])) {
                unlink(ROOTPATH . 'public/uploads/blogs/' . $blog['image']);
            }
        }
        if ($this->blogModel->delete($id)) {
            return $this->respond([
                'status' => 200,
                'message' => 'Blog deleted successfully',
            ]);
        }
        return $this->failServerError('Something went wrong');
    }
}
