/**
 * THEME TOGGLE - Sistema de Alternância Dark/Light Mode
 * Armazena preferência do usuário no localStorage
 */

(function() {
    'use strict';
    
    // Elementos
    let themeToggleBtn = null;
    
    // Função para aplicar o tema
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            document.body.classList.remove('light-mode');
        } else {
            document.body.classList.add('light-mode');
            document.body.classList.remove('dark-mode');
        }
        
        // Atualiza o ícone do botão
        updateToggleButton(theme);
        
        // Salva no localStorage
        localStorage.setItem('theme', theme);
    }
    
    // Função para atualizar o botão
    function updateToggleButton(theme) {
        if (!themeToggleBtn) return;
        
        const icon = themeToggleBtn.querySelector('i');
        const text = themeToggleBtn.querySelector('.theme-text');
        
        if (theme === 'dark') {
            icon.className = 'fas fa-sun';
            if (text) text.textContent = 'Modo Claro';
        } else {
            icon.className = 'fas fa-moon';
            if (text) text.textContent = 'Modo Escuro';
        }
    }
    
    // Função para alternar o tema
    function toggleTheme() {
        const currentTheme = localStorage.getItem('theme') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
        
        // Animação do botão
        if (themeToggleBtn) {
            themeToggleBtn.style.transform = 'scale(0.9)';
            setTimeout(() => {
                themeToggleBtn.style.transform = 'scale(1)';
            }, 150);
        }
    }
    
    // Função para criar o botão de toggle
    function createToggleButton() {
        // Verifica se o botão já existe
        if (document.getElementById('theme-toggle-btn')) {
            themeToggleBtn = document.getElementById('theme-toggle-btn');
            return;
        }
        
        // Cria o botão
        const button = document.createElement('button');
        button.id = 'theme-toggle-btn';
        button.className = 'theme-toggle-btn';
        button.innerHTML = `
            <i class="fas fa-moon"></i>
            <span class="theme-text">Modo Escuro</span>
        `;
        
        // Adiciona evento de clique
        button.addEventListener('click', toggleTheme);
        
        // Adiciona ao body
        document.body.appendChild(button);
        themeToggleBtn = button;
    }
    
    // Função de inicialização
    function init() {
        // Cria o botão
        createToggleButton();
        
        // Verifica se há preferência salva
        const savedTheme = localStorage.getItem('theme');
        
        if (savedTheme) {
            // Usa o tema salvo
            applyTheme(savedTheme);
        } else {
            // Verifica preferência do sistema
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(prefersDark ? 'dark' : 'light');
        }
        
        // Adiciona listener para mudanças na preferência do sistema
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Só aplica se o usuário não tiver preferência salva
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }
    
    // Inicializa quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Expõe funções globalmente para uso externo
    window.ThemeToggle = {
        toggle: toggleTheme,
        setTheme: applyTheme,
        getTheme: function() {
            return localStorage.getItem('theme') || 'light';
        }
    };
    
})();
