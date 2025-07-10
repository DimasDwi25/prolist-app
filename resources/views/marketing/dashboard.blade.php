@extends('marketing.layouts.app')

@section('content')
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
      <div class="text-sm text-gray-500">Total Users</div>
      <div class="text-3xl font-bold">1,234</div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
      <div class="text-sm text-gray-500">Active Projects</div>
      <div class="text-3xl font-bold">87</div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500">
      <div class="text-sm text-gray-500">Revenue</div>
      <div class="text-3xl font-bold">$23,450</div>
    </div>
  </div>

  <div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-xl font-semibold mb-4 text-gray-700">📝 Recent Activities</h2>
    <ul class="divide-y divide-gray-200">
      <li class="py-2">🟢 User <strong>John</strong> created a new project</li>
      <li class="py-2">🟡 Report <strong>#109</strong> generated</li>
      <li class="py-2">🔴 User <strong>Sarah</strong> was removed</li>
    </ul>
  </div>
@endsection
