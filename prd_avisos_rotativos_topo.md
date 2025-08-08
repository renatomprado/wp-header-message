**Product Requirements Document**

# Avisos Rotativos Topo

## 1. Visão Geral

**Objetivo:** Criar um snippet de WordPress (Code Snippets) que exibe mensagens rotativas no topo do site via shortcode `[avisos_rotativos_topo]`, permitindo:

- Configuração amigável via painel do Admin.
- Personalização de estilos (cores, fontes, tamanhos).
- Seleção de efeitos de transição (Fade, Slide, Zoom, Flip H/V, Typewriter, News Ticker Vertical).
- Ordenação e linkagem individual de mensagens.

## 2. Escopo

- **Admin:** página única `Avisos Rotativos` no menu do Admin.
- **Shortcode Frontend:** gera container de mensagens no header.
- **Configurações:** definem comportamento e estilo.
- **Dependência:** jQuery (inclusa no WordPress por padrão).

## 3. Requisitos Funcionais

### 3.1. Admin Panel

- **Localização:** Menu principal do WordPress → `Avisos Rotativos`.
- **Campos:**
  - **Quantidade:** número total de mensagens a exibir.
  - **Mensagens:** para cada item:
    - *Ordem:* inteiro para prioridade na exibição.
    - *Texto:* string (não pode ser vazia).
    - *Link:* URL ou caminho relativo (prefixado com home\_url se necessário).
  - **Cores & Estilo:**
    - *Cor do texto* (color picker).
    - *Cor de fundo* (color picker).
    - *Tamanho da fonte* (em px).
    - *Peso da fonte* (`normal` ou `bold`).
    - *Estilo da fonte* (`normal`, `italic`, `oblique`).
  - **Transição:**
    - *Duração* (ms, mínimo 100).
    - *Gap* (ms de espera antes da próxima transição).
    - *Efeito* (seleção dentre: `fade`, `slide`, `zoom`, `flip`, `flipVertical`, `typewriter`, `newsTicker`).
- **Segurança:** usa `wp_nonce_field` e `current_user_can('manage_options')` implicitamente.

### 3.2. Shortcode Frontend

- **Tag:** `[avisos_rotativos_topo]`.
- **Markup:**
  ```html
  <div id="rotativo_container" style="background:{bg};overflow:hidden;padding:5px;">
    <!-- mensagens em div.rotativo_msg -->
  </div>
  ```
- **Cada mensagem:**
  - Container `<div class="rotativo_msg" data-full-text="...">` com estilo inline.
  - Link `<a>` com `href` e estilização.
- **Animações:** implementadas via jQuery:
  - loop contínuo (`setInterval`).
  - transições conforme efeito.
  - `<div>` permanece em fluxo natural (sem `position:absolute`).

## 4. Efeitos de Transição Suportados

| Efeito            | Descrição                                                                       |
| ----------------- | ------------------------------------------------------------------------------- |
| **Fade**          | `fadeOut` e `fadeIn` jQuery, alternância de opacidade.                          |
| **Slide**         | `slideUp` e `slideDown` jQuery, alternância vertical suave.                     |
| **Zoom**          | `fadeOut`, depois `transform: scale()` com transição CSS.                       |
| **Flip H (flip)** | Rotação 3D no eixo Y, simulando virada horizontal de cartões.                   |
| **Flip V**        | Rotação 3D no eixo X, simulando virada vertical de cartões.                     |
| **Typewriter**    | Aparecimento de texto letra-a-letra, baseado em `data-full-text`.               |
| **News Ticker**   | Scroll contínuo vertical, usando `animate({scrollTop})` jQuery e loop infinito. |

## 5. Fluxo de Usuário

1. Instalar e ativar o snippet no plugin Code Snippets.
2. Acessar **Avisos Rotativos** no Admin.
3. Definir quantidade e configurar cada mensagem (ordem, texto, link).
4. Ajustar cores, fontes, duração, gap e escolher o efeito desejado.
5. Salvar configurações.
6. Inserir `[avisos_rotativos_topo]` no header ou bloco de texto (ex: via WoodMart Header).
7. Visualizar mensagens rotativas no frontend.

## 6. Critérios de Aceitação

- ✅ Admin reconhece até N mensagens e salva corretamente as opções.
- ✅ Shortcode renderiza container com N mensagens aplicando estilos corretos.
- ✅ Transições fluem sem gaps excessivos (gap configurável).
- ✅ Todas as opções de efeito funcionam conforme documentação.
- ✅ Responsivo: loop automático em mobile e desktop.
- ✅ Sem deformação no layout, respeitando classes do tema (sem `position:absolute`).

## 7. Considerações Técnicas

- **Desempenho:** mínimo uso de DOM, jQuery animado somente no container.
- **Carga de scripts:** inline no footer do shortcode, contornando cache agressivo.
- **Compatibilidade:** WP ≥ 5.0, jQuery padrão, PHP ≥ 7.2.
- **Futuras melhorias:** suporte a mais efeitos (bounce, typewriter inverso), timing individual por item, pausa ao passar o mouse.

---

*Documento gerado para referência da implementação dos Avisos Rotativos Topo.*

