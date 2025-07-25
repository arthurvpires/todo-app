@component('mail::message')
# Olá, {{ $user->name }}!

Estas são as tarefas que vencem **amanhã ({{ now()->addDay()->format('d/m/Y') }})**:

@foreach ($tasks as $task)
    - {{ $task->title }} : {{ $task->description }}
@endforeach

Atenciosamente,  
Equipe Todo App
@endcomponent
