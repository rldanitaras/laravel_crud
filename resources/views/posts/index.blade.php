<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Posts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Create Button -->
                    <button onclick="openCreateModal()" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded">
                        Create Post
                    </button>

                    <!-- Table -->
                    <div class="mt-6 bg-white shadow rounded">
                        <table class="w-full text-left">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-3">Title</th>
                                    <th class="p-3">Content</th>
                                    <th class="p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="postTableBody">
                                @foreach($posts as $post)
                                <tr id="post-{{ $post->id }}" class="border-b">
                                    <td class="p-3">{{ $post->title }}</td>
                                    <td class="p-3">{{ $post->content }}</td>
                                    <td class="p-3 space-x-2">
                                        <button onclick="openEditModal({{ $post->id }}, @js($post->title), @js($post->content))"
                                            class="bg-yellow-500 text-white px-2 py-1 rounded">
                                            Edit
                                        </button>

                                        <button type="button"
                                            onclick="deletePost({{ $post->id }})"
                                            class="bg-red-600 text-white px-2 py-1 rounded">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="postModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white w-1/3 p-6 rounded shadow">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 id="modalTitle" class="text-xl font-bold text-gray-800">
                    Create Post
                </h2>

                <button onclick="closeModal()" 
                    class="text-gray-500 hover:text-gray-700 text-xl">
                    &times;
                </button>
            </div>
            <form id="postForm">
                @csrf
                <input type="hidden" id="methodField" name="_method">

                <div class="mb-4">
                    <label>Title</label>
                    <input type="text" name="title" id="title"
                        class="w-full border rounded p-2">
                </div>

                <div class="mb-4">
                    <label>Content</label>
                    <textarea name="content" id="content"
                        class="w-full border rounded p-2"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-500 text-white px-3 py-1 rounded">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-3 py-1 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <!-- script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script -->

    {{-- @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
    @endif --}}

    <script>
        async function handleRequest(url, method, data = null, onSuccess = null) {
            try {
                let options = {
                    method: method,
                    headers: {
                        //'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                };

                if (data) options.body = JSON.stringify(data);

                const response = await fetch(url, options);

                let result;
                try {
                    result = await response.json();
                } catch {
                    throw { message: 'Invalid server response' };
                }

                if (!response.ok) throw result;

                Swal.fire({
                    icon: 'success',
                    title: result.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                if (onSuccess) onSuccess(result);

                return result;

            } catch (error) {
                let errorMsg = '';

                if (error.errors) {
                    Object.values(error.errors).forEach(err => {
                        errorMsg += err[0] + '<br>';
                    });
                } else {
                    errorMsg = error.message || 'Something went wrong';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMsg
                });
            }
        }
    </script>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').innerText = "Create Post";
            document.getElementById('postForm').action = "{{ route('posts.store') }}";
            document.getElementById('methodField').value = '';
            document.getElementById('title').value = '';
            document.getElementById('content').value = '';
            document.getElementById('postModal').classList.remove('hidden');
        }

        function openEditModal(id, title, content) {
            document.getElementById('modalTitle').innerText = "Edit Post";
            document.getElementById('postForm').action = "/posts/" + id;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('title').value = title;
            document.getElementById('content').value = content;
            document.getElementById('postModal').classList.remove('hidden');
        }

        document.getElementById('postForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const method = document.getElementById('methodField').value;
            const isEdit = method === 'PUT';

            const url = isEdit ? this.action : "{{ route('posts.store') }}";

            const httpMethod = isEdit ? 'PUT' : 'POST';

            const data = {
                title: this.title.value,
                content: this.content.value
            };

            handleRequest(url, httpMethod, data, (result) => {

                closeModal();
                this.reset();

                if (isEdit) {
                    updatePostInTable(result.data);
                } else {
                    appendPostToTable(result.data);
                }
            });
        });

        function appendPostToTable(post) {
            const table = document.getElementById('postTableBody');

            const row = document.createElement('tr');
            row.classList.add('border-b');
            row.id = `post-${post.id}`;

            row.innerHTML = `
                <td class="p-3">${post.title}</td>
                <td class="p-3">${post.content}</td>
                <td class="p-3 space-x-2">
                    <button onclick='openEditModal(${post.id}, ${JSON.stringify(post.title)}, ${JSON.stringify(post.content)})'
                        class="bg-yellow-500 text-white px-2 py-1 rounded">
                        Edit
                    </button>
                    <button onclick="deletePost(${post.id})"
                        class="bg-red-600 text-white px-2 py-1 rounded">
                        Delete
                    </button>
                </td>
            `;

            table.prepend(row);
        }

        function updatePostInTable(post) {
            const row = document.getElementById(`post-${post.id}`);

            if (!row) return;

            row.innerHTML = `
                <td class="p-3">${post.title}</td>
                <td class="p-3">${post.content}</td>
                <td class="p-3 space-x-2">
                    <button onclick='openEditModal(${post.id}, ${JSON.stringify(post.title)}, ${JSON.stringify(post.content)})'
                        class="bg-yellow-500 text-white px-2 py-1 rounded">
                        Edit
                    </button>
                    <button onclick="deletePost(${post.id})"
                        class="bg-red-600 text-white px-2 py-1 rounded">
                        Delete
                    </button>
                </td>
            `;
        }

        function closeModal() {
            document.getElementById('postModal').classList.add('hidden');
        }

        function deletePost(id) {
            Swal.fire({
                title: 'Delete Post',
                text: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    handleRequest(`/posts/${id}`, 'DELETE', null, () => {
                        const row = document.getElementById(`post-${id}`);
                        if (row) row.remove();
                    });

                }
            });
        }
    </script>

</x-app-layout>
