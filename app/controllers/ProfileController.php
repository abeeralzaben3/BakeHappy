<?php
class ProfileController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    public function index(): void
    {
        $user = (new User())->find(Auth::id());
        $this->view('user/profile/index', compact('user'));
    }
}
