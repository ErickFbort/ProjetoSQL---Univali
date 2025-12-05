// Configuração da API
// Detectar se está rodando via file:// ou http://
const isFileProtocol = window.location.protocol === 'file:';
const API_URL = isFileProtocol ? null : 'api.php'; // Só funciona via servidor HTTP

// Variáveis globais
let processos = [];
let empresas = [];
let responsaveis = [];
let processoEditando = null;
let processoParaDeletar = null;
let isLoading = false;

// Elementos do DOM
const form = document.getElementById('process-form');
const formTitle = document.getElementById('form-title');
const submitBtn = document.getElementById('submit-btn');
const cancelBtn = document.getElementById('cancel-btn');
const processList = document.getElementById('process-list');
const emptyState = document.getElementById('empty-state');
const searchInput = document.getElementById('search-input');
const searchBtn = document.getElementById('search-btn');
const confirmModal = document.getElementById('confirm-modal');
const confirmDeleteBtn = document.getElementById('confirm-delete');
const cancelDeleteBtn = document.getElementById('cancel-delete');

// Inicialização
document.addEventListener('DOMContentLoaded', async () => {
    verificarAmbiente();
    configurarEventos();
    if (API_URL) {
        await carregarEmpresas();
        await carregarResponsaveis();
    }
});

// Verificar se está rodando em ambiente adequado
function verificarAmbiente() {
    if (isFileProtocol) {
        mostrarAvisoServidor();
        // Usar localStorage como fallback temporário
        carregarProcessosLocalStorage();
    } else {
        carregarProcessos();
    }
}

// Mostrar aviso sobre necessidade de servidor
function mostrarAvisoServidor() {
    const aviso = document.createElement('div');
    aviso.id = 'aviso-servidor';
    aviso.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #ff6b35 0%, #e55a2b 100%);
        color: white;
        padding: 20px;
        z-index: 10000;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        text-align: center;
    `;
    aviso.innerHTML = `
        <div style="max-width: 1200px; margin: 0 auto;">
            <strong>⚠️ ATENÇÃO: Servidor Necessário</strong>
            <p style="margin: 10px 0; font-size: 0.9em;">
                Este sistema precisa rodar em um servidor web para funcionar corretamente com MySQL.
                <br>
                <strong>Execute no terminal:</strong> <code style="background: rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 4px;">php -S localhost:8000</code>
                <br>
                Depois acesse: <code style="background: rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 4px;">http://localhost:8000/index.html</code>
                <br>
                <small style="opacity: 0.9;">Por enquanto, os dados serão salvos localmente no navegador.</small>
            </p>
            <button onclick="this.parentElement.parentElement.remove(); document.body.style.paddingTop = '0';" style="background: white; color: #ff6b35; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 10px;">
                Entendi, continuar mesmo assim
            </button>
        </div>
    `;
    document.body.insertBefore(aviso, document.body.firstChild);
    
    // Ajustar padding do body para não sobrepor o aviso
    document.body.style.paddingTop = '120px';
}

// Configurar eventos
function configurarEventos() {
    form.addEventListener('submit', handleSubmit);
    cancelBtn.addEventListener('click', cancelarEdicao);
    searchInput.addEventListener('input', debounce(filtrarProcessos, 300));
    searchBtn.addEventListener('click', () => filtrarProcessos());
    confirmDeleteBtn.addEventListener('click', confirmarExclusao);
    cancelDeleteBtn.addEventListener('click', fecharModal);
    
    // Fechar modal ao clicar fora
    confirmModal.addEventListener('click', (e) => {
        if (e.target === confirmModal) {
            fecharModal();
        }
    });
}

// Debounce para otimizar buscas
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Manipular submit do formulário
async function handleSubmit(e) {
    e.preventDefault();
    
    if (isLoading) return;
    
    const dados = coletarDadosFormulario();
    
    try {
        setLoading(true);
        
        if (processoEditando !== null) {
            await atualizarProcesso(processoEditando, dados);
            mostrarMensagem('Processo atualizado com sucesso!', 'success');
        } else {
            await criarProcesso(dados);
            mostrarMensagem('Processo cadastrado com sucesso!', 'success');
        }
        
        form.reset();
        processoEditando = null;
        atualizarInterfaceFormulario();
        
        // Recarregar processos (funciona com API e localStorage)
        if (API_URL) {
            await carregarProcessos();
        } else {
            carregarProcessosLocalStorage();
        }
    } catch (error) {
        mostrarMensagem('Erro ao salvar processo: ' + error.message, 'error');
    } finally {
        setLoading(false);
    }
}

// Coletar dados do formulário
function coletarDadosFormulario() {
    return {
        numero_processo: document.getElementById('numero-processo').value.trim(),
        tipo_processo: document.getElementById('tipo-processo').value,
        empresa_id: parseInt(document.getElementById('empresa').value),
        responsavel_id: parseInt(document.getElementById('responsavel').value),
        data_inicio: document.getElementById('data-inicio').value,
        data_prevista: document.getElementById('data-prevista').value || null,
        status: document.getElementById('status').value,
        observacoes: document.getElementById('observacoes').value.trim() || null
    };
}

// ============================================
// CRUD - Operações com a API
// ============================================

// CREATE - Criar novo processo
async function criarProcesso(dados) {
    if (!API_URL) {
        // Fallback para localStorage
        return criarProcessoLocalStorage(dados);
    }
    
    const response = await fetch(API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dados)
    });
    
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Erro ao criar processo');
    }
    
    return await response.json();
}

// READ - Carregar todos os processos
async function carregarProcessos() {
    if (!API_URL) {
        // Fallback para localStorage
        carregarProcessosLocalStorage();
        return;
    }
    
    try {
        setLoading(true);
        const response = await fetch(API_URL);
        
        if (!response.ok) {
            throw new Error('Erro ao carregar processos');
        }
        
        processos = await response.json();
        renderizarProcessos();
    } catch (error) {
        console.error('Erro ao carregar processos:', error);
        mostrarMensagem('Erro ao carregar processos. Verifique se a API está funcionando.', 'error');
        processos = [];
        renderizarProcessos();
    } finally {
        setLoading(false);
    }
}

// READ - Buscar processo por ID
async function buscarProcessoPorId(id) {
    if (!API_URL) {
        // Fallback para localStorage
        return processos.find(p => p.id === id);
    }
    
    const response = await fetch(`${API_URL}?id=${id}`);
    
    if (!response.ok) {
        throw new Error('Erro ao buscar processo');
    }
    
    return await response.json();
}

// UPDATE - Atualizar processo existente
async function atualizarProcesso(id, dados) {
    if (!API_URL) {
        // Fallback para localStorage
        return atualizarProcessoLocalStorage(id, dados);
    }
    
    const response = await fetch(API_URL, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: id,
            ...dados
        })
    });
    
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Erro ao atualizar processo');
    }
    
    return await response.json();
}

// DELETE - Excluir processo
async function deletarProcessoAPI(id) {
    if (!API_URL) {
        // Fallback para localStorage
        return deletarProcessoLocalStorage(id);
    }
    
    const response = await fetch(`${API_URL}?id=${id}`, {
        method: 'DELETE'
    });
    
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Erro ao excluir processo');
    }
    
    return await response.json();
}

// SEARCH - Buscar processos
async function buscarProcessos(termo) {
    if (!termo || termo.trim() === '') {
        await carregarProcessos();
        return;
    }
    
    if (!API_URL) {
        // Fallback para localStorage
        filtrarProcessosLocalStorage(termo);
        return;
    }
    
    try {
        setLoading(true);
        const response = await fetch(`${API_URL}?search=${encodeURIComponent(termo)}`);
        
        if (!response.ok) {
            throw new Error('Erro ao buscar processos');
        }
        
        processos = await response.json();
        renderizarProcessos();
    } catch (error) {
        console.error('Erro ao buscar processos:', error);
        mostrarMensagem('Erro ao buscar processos', 'error');
    } finally {
        setLoading(false);
    }
}

// ============================================
// Interface e Interações
// ============================================

// Editar processo (disponível globalmente para onclick)
window.editarProcesso = async function(id) {
    try {
        setLoading(true);
        let processo;
        
        if (API_URL) {
            processo = await buscarProcessoPorId(id);
        } else {
            processo = processos.find(p => p.id === id);
        }
        
        if (!processo) {
            mostrarMensagem('Processo não encontrado', 'error');
            return;
        }
        
        processoEditando = id;
        
        // Preencher formulário (adaptar nomes dos campos - suporta ambos os formatos)
        const numeroProcesso = processo.numero_processo || processo.numeroProcesso || '';
        const tipoProcesso = processo.tipo_processo || processo.tipoProcesso || '';
        const dataInicio = processo.data_inicio || processo.dataInicio || '';
        const dataPrevista = processo.data_prevista || processo.dataPrevista || '';
        const empresaId = processo.empresa_id || processo.empresaId || '';
        const responsavelId = processo.responsavel_id || processo.responsavelId || '';
        
        document.getElementById('numero-processo').value = numeroProcesso;
        document.getElementById('tipo-processo').value = tipoProcesso;
        document.getElementById('empresa').value = empresaId;
        document.getElementById('responsavel').value = responsavelId;
        document.getElementById('data-inicio').value = dataInicio;
        document.getElementById('data-prevista').value = dataPrevista;
        document.getElementById('status').value = processo.status || '';
        document.getElementById('observacoes').value = processo.observacoes || '';
        
        atualizarInterfaceFormulario();
        
        // Scroll para o formulário
        document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
    } catch (error) {
        mostrarMensagem('Erro ao carregar processo para edição', 'error');
    } finally {
        setLoading(false);
    }
}

// Deletar processo (abrir modal) - disponível globalmente para onclick
window.deletarProcesso = function(id) {
    processoParaDeletar = id;
    confirmModal.classList.add('show');
}

// Confirmar exclusão
async function confirmarExclusao() {
    if (processoParaDeletar !== null) {
        try {
            setLoading(true);
            await deletarProcessoAPI(processoParaDeletar);
            mostrarMensagem('Processo excluído com sucesso!', 'success');
            processoParaDeletar = null;
            
            // Recarregar processos (funciona com API e localStorage)
            if (API_URL) {
                await carregarProcessos();
            } else {
                carregarProcessosLocalStorage();
            }
        } catch (error) {
            mostrarMensagem('Erro ao excluir processo: ' + error.message, 'error');
        } finally {
            setLoading(false);
        }
    }
    fecharModal();
}

// Fechar modal
function fecharModal() {
    confirmModal.classList.remove('show');
    processoParaDeletar = null;
}

// Cancelar edição
function cancelarEdicao() {
    form.reset();
    processoEditando = null;
    atualizarInterfaceFormulario();
    mostrarMensagem('Edição cancelada', 'info');
}

// Atualizar interface do formulário
function atualizarInterfaceFormulario() {
    if (processoEditando !== null) {
        formTitle.textContent = 'Editar Processo Aéreo';
        submitBtn.innerHTML = 'Atualizar Processo <span class="btn-arrow">→</span>';
        cancelBtn.style.display = 'block';
    } else {
        formTitle.textContent = 'Cadastrar Novo Processo Aéreo';
        submitBtn.innerHTML = 'Cadastrar Processo <span class="btn-arrow">→</span>';
        cancelBtn.style.display = 'none';
    }
}

// Renderizar lista de processos
function renderizarProcessos(processosFiltrados = null) {
    const processosParaRenderizar = processosFiltrados || processos;
    
    if (processosParaRenderizar.length === 0) {
        processList.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }
    
    emptyState.classList.add('hidden');
    
    processList.innerHTML = processosParaRenderizar.map(processo => {
        // Adaptar nomes dos campos (API retorna com underscore)
        const numeroProcesso = processo.numero_processo || processo.numeroProcesso;
        const tipoProcesso = processo.tipo_processo || processo.tipoProcesso;
        const status = processo.status;
        const dataInicio = processo.data_inicio || processo.dataInicio;
        const dataPrevista = processo.data_prevista || processo.dataPrevista;
        const observacoes = processo.observacoes;
        
        const statusClass = `status-${status.toLowerCase().replace(/\s+/g, '-').normalize('NFD').replace(/[\u0300-\u036f]/g, '')}`;
        const dataInicioFormatada = formatarData(dataInicio);
        const dataPrevistaFormatada = dataPrevista ? formatarData(dataPrevista) : 'Não informada';
        const id = processo.id;
        
        return `
            <div class="process-card">
                <div class="process-header">
                    <div class="process-number">${numeroProcesso}</div>
                    <span class="process-status ${statusClass}">${status}</span>
                </div>
                <div class="process-info">
                    <div class="info-item">
                        <span class="info-label">Tipo de Processo</span>
                        <span class="info-value">${tipoProcesso}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Empresa</span>
                        <span class="info-value">${processo.empresa_nome || processo.empresa || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Responsável</span>
                        <span class="info-value">${processo.responsavel_nome || processo.responsavel || 'N/A'}${processo.responsavel_cargo ? ' - ' + processo.responsavel_cargo : ''}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Data de Início</span>
                        <span class="info-value">${dataInicioFormatada}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Data Prevista</span>
                        <span class="info-value">${dataPrevistaFormatada}</span>
                    </div>
                </div>
                ${observacoes ? `
                    <div class="info-item" style="margin-top: 10px;">
                        <span class="info-label">Observações</span>
                        <span class="info-value">${observacoes}</span>
                    </div>
                ` : ''}
                <div class="process-actions">
                    <button class="btn btn-edit" data-action="edit" data-id="${id}" ${isLoading ? 'disabled' : ''}>
                        ✏️ Editar
                    </button>
                    <button class="btn btn-delete" data-action="delete" data-id="${id}" ${isLoading ? 'disabled' : ''}>
                        🗑️ Excluir
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    // Adicionar event listeners aos botões após renderizar
    adicionarEventListenersBotoes();
}

// Adicionar event listeners aos botões de ação
function adicionarEventListenersBotoes() {
    // Remover listeners anteriores para evitar duplicação
    const botoesEdit = processList.querySelectorAll('[data-action="edit"]');
    const botoesDelete = processList.querySelectorAll('[data-action="delete"]');
    
    botoesEdit.forEach(btn => {
        // Remover listener anterior se existir
        btn.replaceWith(btn.cloneNode(true));
    });
    
    botoesDelete.forEach(btn => {
        // Remover listener anterior se existir
        btn.replaceWith(btn.cloneNode(true));
    });
    
    // Adicionar novos listeners
    processList.querySelectorAll('[data-action="edit"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const id = parseInt(btn.getAttribute('data-id'));
            if (id && !isLoading) {
                editarProcesso(id);
            }
        });
    });
    
    processList.querySelectorAll('[data-action="delete"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const id = parseInt(btn.getAttribute('data-id'));
            if (id && !isLoading) {
                deletarProcesso(id);
            }
        });
    });
}

// Filtrar processos
async function filtrarProcessos() {
    const termo = searchInput.value.trim();
    
    if (termo === '') {
        await carregarProcessos();
        return;
    }
    
    await buscarProcessos(termo);
}

// Formatar data
function formatarData(dataString) {
    if (!dataString) return 'Não informada';
    
    // Se já estiver no formato brasileiro, retornar como está
    if (dataString.includes('/')) {
        return dataString;
    }
    
    // Converter de YYYY-MM-DD para DD/MM/YYYY
    const data = new Date(dataString + 'T00:00:00');
    if (isNaN(data.getTime())) {
        return dataString; // Retornar original se não for uma data válida
    }
    return data.toLocaleDateString('pt-BR');
}

// Controlar estado de loading
function setLoading(loading) {
    isLoading = loading;
    submitBtn.disabled = loading;
    
    if (loading) {
        submitBtn.style.opacity = '0.6';
        submitBtn.style.cursor = 'not-allowed';
    } else {
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
    }
}

// Mostrar mensagem
function mostrarMensagem(mensagem, tipo) {
    // Criar elemento de mensagem
    const mensagemEl = document.createElement('div');
    mensagemEl.className = `mensagem mensagem-${tipo}`;
    mensagemEl.textContent = mensagem;
    
    const bgColor = tipo === 'success' ? '#ff6b35' : tipo === 'error' ? '#dc3545' : '#17a2b8';
    
    mensagemEl.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${bgColor};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        z-index: 2000;
        animation: slideIn 0.3s ease;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.2);
        max-width: 400px;
    `;
    
    document.body.appendChild(mensagemEl);
    
    setTimeout(() => {
        mensagemEl.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => mensagemEl.remove(), 300);
    }, 4000);
}

// ============================================
// FUNÇÕES PARA EMPRESAS E RESPONSÁVEIS
// ============================================

// Carregar empresas
async function carregarEmpresas() {
    if (!API_URL) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}?entity=empresas`);
        if (!response.ok) {
            throw new Error('Erro ao carregar empresas');
        }
        empresas = await response.json();
        preencherSelectEmpresas();
    } catch (error) {
        console.error('Erro ao carregar empresas:', error);
        mostrarMensagem('Erro ao carregar empresas', 'error');
    }
}

// Preencher select de empresas
function preencherSelectEmpresas() {
    const selectEmpresa = document.getElementById('empresa');
    if (!selectEmpresa) return;
    
    selectEmpresa.innerHTML = '<option value="">Selecione a empresa</option>';
    
    empresas.forEach(empresa => {
        const option = document.createElement('option');
        option.value = empresa.id;
        option.textContent = empresa.nome + (empresa.cnpj ? ` (${empresa.cnpj})` : '');
        selectEmpresa.appendChild(option);
    });
}

// Carregar responsáveis
async function carregarResponsaveis() {
    if (!API_URL) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}?entity=responsaveis`);
        if (!response.ok) {
            throw new Error('Erro ao carregar responsáveis');
        }
        responsaveis = await response.json();
        preencherSelectResponsaveis();
    } catch (error) {
        console.error('Erro ao carregar responsáveis:', error);
        mostrarMensagem('Erro ao carregar responsáveis', 'error');
    }
}

// Preencher select de responsáveis
function preencherSelectResponsaveis() {
    const selectResponsavel = document.getElementById('responsavel');
    if (!selectResponsavel) return;
    
    selectResponsavel.innerHTML = '<option value="">Selecione o responsável</option>';
    
    responsaveis.forEach(responsavel => {
        const option = document.createElement('option');
        option.value = responsavel.id;
        option.textContent = responsavel.nome + (responsavel.cargo ? ` - ${responsavel.cargo}` : '');
        selectResponsavel.appendChild(option);
    });
}

// ============================================
// FALLBACK: Funções para localStorage (quando file://)
// ============================================

function carregarProcessosLocalStorage() {
    try {
        const dados = localStorage.getItem('processosAereos');
        processos = dados ? JSON.parse(dados) : [];
        renderizarProcessos();
    } catch (error) {
        console.error('Erro ao carregar do localStorage:', error);
        processos = [];
        renderizarProcessos();
    }
}

function criarProcessoLocalStorage(dados) {
    const novoProcesso = {
        id: Date.now(),
        numero_processo: dados.numero_processo,
        tipo_processo: dados.tipo_processo,
        empresa: dados.empresa,
        responsavel: dados.responsavel,
        data_inicio: dados.data_inicio,
        data_prevista: dados.data_prevista,
        status: dados.status,
        observacoes: dados.observacoes,
        data_criacao: new Date().toISOString()
    };
    
    processos.push(novoProcesso);
    salvarNoLocalStorage();
    return novoProcesso;
}

function atualizarProcessoLocalStorage(id, dados) {
    const index = processos.findIndex(p => p.id === id);
    if (index !== -1) {
        processos[index] = {
            ...processos[index],
            ...dados,
            data_atualizacao: new Date().toISOString()
        };
        salvarNoLocalStorage();
        return processos[index];
    }
    throw new Error('Processo não encontrado');
}

function deletarProcessoLocalStorage(id) {
    processos = processos.filter(p => p.id !== id);
    salvarNoLocalStorage();
    return { message: 'Processo excluído com sucesso' };
}

function filtrarProcessosLocalStorage(termo) {
    const termoLower = termo.toLowerCase();
    const processosFiltrados = processos.filter(processo => {
        return (
            (processo.numero_processo && processo.numero_processo.toLowerCase().includes(termoLower)) ||
            (processo.tipo_processo && processo.tipo_processo.toLowerCase().includes(termoLower)) ||
            (processo.empresa && processo.empresa.toLowerCase().includes(termoLower)) ||
            (processo.responsavel && processo.responsavel.toLowerCase().includes(termoLower)) ||
            (processo.status && processo.status.toLowerCase().includes(termoLower)) ||
            (processo.observacoes && processo.observacoes.toLowerCase().includes(termoLower))
        );
    });
    renderizarProcessos(processosFiltrados);
}

function salvarNoLocalStorage() {
    try {
        localStorage.setItem('processosAereos', JSON.stringify(processos));
    } catch (error) {
        console.error('Erro ao salvar no localStorage:', error);
        mostrarMensagem('Erro ao salvar dados localmente', 'error');
    }
}

// Adicionar animações CSS dinamicamente
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
    
    .btn:disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
    }
`;
document.head.appendChild(style);
