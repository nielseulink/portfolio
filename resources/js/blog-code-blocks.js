import hljs from 'highlight.js/lib/core';
import bash from 'highlight.js/lib/languages/bash';
import javascript from 'highlight.js/lib/languages/javascript';
import json from 'highlight.js/lib/languages/json';
import php from 'highlight.js/lib/languages/php';
import yaml from 'highlight.js/lib/languages/yaml';
import 'highlight.js/styles/atom-one-dark.min.css';

hljs.registerLanguage('bash', bash);
hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('json', json);
hljs.registerLanguage('php', php);
hljs.registerLanguage('yaml', yaml);

window.hljs = hljs;

let lineNumbersPluginReady = null;

function ensureLineNumbersPlugin() {
    if (! lineNumbersPluginReady) {
        lineNumbersPluginReady = import('highlightjs-line-numbers.js');
    }

    return lineNumbersPluginReady;
}

function languageLabel(code) {
    const languageClass = [...code.classList].find((className) => className.startsWith('language-'));

    if (! languageClass) {
        return 'text';
    }

    return languageClass.replace('language-', '');
}

function highlightCode(code) {
    const language = languageLabel(code);

    if (language !== 'text' && hljs.getLanguage(language)) {
        hljs.highlightElement(code);

        return;
    }

    const { value } = hljs.highlightAuto(code.textContent ?? '', ['yaml', 'php', 'javascript', 'bash', 'json']);
    code.innerHTML = value;
    code.classList.add('hljs');
}

function showCopyToast() {
    window.Flux?.toast({
        heading: 'copied to clipboard',
        variant: 'success',
        duration: 3000,
    });
}

function showCopyErrorToast() {
    window.Flux?.toast({
        heading: 'Copy failed',
        variant: 'danger',
        duration: 4000,
    });
}

function copyTextFallback(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'fixed';
    textarea.style.top = '0';
    textarea.style.left = '0';
    textarea.style.opacity = '0';
    textarea.style.pointerEvents = 'none';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    textarea.setSelectionRange(0, text.length);

    let success = false;

    try {
        success = document.execCommand('copy');
    } finally {
        document.body.removeChild(textarea);
    }

    return success;
}

function copyCode(rawText) {
    if (copyTextFallback(rawText)) {
        showCopyToast();

        return;
    }

    if (! navigator.clipboard?.writeText || ! window.isSecureContext) {
        showCopyErrorToast();

        return;
    }

    navigator.clipboard.writeText(rawText)
        .then(() => showCopyToast())
        .catch(() => showCopyErrorToast());
}

function lineCount(rawText) {
    if (rawText === '') {
        return 0;
    }

    return rawText.split('\n').length;
}

function makeCollapsible(figure, rawText) {
    const totalLines = lineCount(rawText);
    const collapsedLineLimit = 10;

    if (totalLines <= collapsedLineLimit) {
        return;
    }

    figure.classList.add('is-collapsed');
    figure.dataset.collapsible = 'true';
    figure.dataset.totalLines = String(totalLines);

    const footer = document.createElement('div');
    footer.className = 'code-block-footer';

    const expandButton = document.createElement('button');
    expandButton.type = 'button';
    expandButton.className = 'code-block-expand';
    expandButton.textContent = `Show all ${totalLines} lines`;

    expandButton.addEventListener('click', () => {
        const isCollapsed = figure.classList.toggle('is-collapsed');

        expandButton.textContent = isCollapsed
            ? `Show all ${totalLines} lines`
            : 'Show less';
    });

    footer.append(expandButton);
    figure.append(footer);
}

async function enhanceCodeBlocks(root = document) {
    await ensureLineNumbersPlugin();

    root.querySelectorAll('.prose-blog pre:not([data-enhanced])').forEach((pre) => {
        pre.dataset.enhanced = 'true';

        const code = pre.querySelector('code');

        if (! code) {
            return;
        }

        const rawText = code.textContent ?? '';

        const figure = document.createElement('figure');
        figure.className = 'code-block';

        const header = document.createElement('div');
        header.className = 'code-block-header';

        const label = document.createElement('span');
        label.className = 'code-block-lang';
        label.textContent = languageLabel(code);

        const copyButton = document.createElement('button');
        copyButton.type = 'button';
        copyButton.className = 'code-block-copy';
        copyButton.textContent = 'Copy';
        copyButton.addEventListener('click', () => {
            copyCode(rawText);
        });

        header.append(label, copyButton);

        pre.parentNode?.insertBefore(figure, pre);
        figure.append(header, pre);

        highlightCode(code);

        if (typeof hljs.lineNumbersBlock === 'function') {
            hljs.lineNumbersBlock(code);
        }

        makeCollapsible(figure, rawText);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    void enhanceCodeBlocks();
});

document.addEventListener('livewire:navigated', () => {
    void enhanceCodeBlocks();
});
