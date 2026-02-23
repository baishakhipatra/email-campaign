<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EmailTemplateController extends Controller
{

    public function index(): View
    {
        $templates = EmailTemplate::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('templates.index', ['templates' => $templates]);
    }


    public function create(): View
    {
        return view('templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
        ]);

        $template = EmailTemplate::create([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['name']),
            'description' => $validated['description'],
            'html_content' => $validated['html_content'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('templates.show', $template)
            ->with('success', 'Template created successfully');
    }


    public function show(EmailTemplate $template): View
    {
        return view('templates.show', ['template' => $template]);
    }


    public function edit(EmailTemplate $template): View
    {
        return view('templates.edit', ['template' => $template]);
    }


    public function update(Request $request, EmailTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
        ]);

        $template->update($validated);

        return redirect()->route('templates.show', $template)
            ->with('success', 'Template updated successfully');
    }


    public function destroy(EmailTemplate $template): RedirectResponse
    {
        if ($template->campaigns()->exists()) {
            return back()->with('error', 'This template is already used in campaigns and cannot be deleted.');
        }

        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template deleted successfully');
    }
}
