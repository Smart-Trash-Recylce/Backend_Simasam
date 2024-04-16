<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user",
     *     tags={"user"},
     *     summary="Get List of Users",
     *     description="Returns a list of all users.",
     *     operationId="index",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = User::all();
        return response()->json(['result' => $user]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/user",
     *     tags={"user"},
     *     summary="Create a User",
     *     description="Creates a new user.",
     *     operationId="store",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Validasi data yang diterima dari request
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Buat user baru berdasarkan data yang diterima
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Enkripsi password sebelum disimpan
        ]);

        // Simpan user ke dalam database
        $user->save();

        // Kembalikan respons JSON yang menyatakan berhasil menambahkan user
        return response()->json(['message' => 'User created successfully'], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     tags={"user"},
     *     summary="Get User by ID",
     *     description="Returns a single user by ID.",
     *     operationId="getUserById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['result' => $user]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/user/{id}",
     *     tags={"user"},
     *     summary="Update a User",
     *     description="Updates an existing user.",
     *     operationId="update",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Cari user berdasarkan ID
        $user = User::find($id);

        // Jika user tidak ditemukan, kembalikan respons JSON dengan kode status 404 (Not Found)
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validasi data yang diterima dari request
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6', // Hanya validasi jika password disertakan dalam request
        ]);

        // Perbarui data user berdasarkan data yang diterima
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('password')) {
            $user->password = bcrypt($request->password); // Enkripsi password jika disertakan dalam request
        }

        // Simpan perubahan ke dalam database
        $user->save();

        // Kembalikan respons JSON yang menyatakan berhasil memperbarui user
        return response()->json(['message' => 'User updated successfully'], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/user/{id}",
     *     tags={"user"},
     *     summary="Delete a User",
     *     description="Deletes an existing user.",
     *     operationId="destroy",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        // Cari user berdasarkan ID
        $user = User::find($id);

        // Jika user tidak ditemukan, kembalikan respons JSON dengan kode status 404 (Not Found)
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Hapus user dari database
        $user->delete();

        // Kembalikan respons JSON yang menyatakan berhasil menghapus user
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
