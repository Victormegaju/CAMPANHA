# Captura de Contatos WhatsApp

Este módulo permite capturar contatos do WhatsApp através da Evolution API.

## Funcionalidades

- **Conexão com WhatsApp via QR Code**: Conecta-se ao WhatsApp usando a Evolution API
- **Captura de Contatos de Duas Formas**:
  1. **Lista de Contatos**: Captura todos os contatos salvos no WhatsApp
  2. **Conversas Ativas**: Captura contatos de todas as conversas/chats
- **Visualização de Contatos**: Exibe todos os contatos capturados em uma tabela organizada
- **Estatísticas**: Mostra totais de contatos por origem
- **Exportação**: Permite baixar a lista de contatos em formato CSV
- **Gerenciamento**: Permite limpar todos os contatos capturados

## Como Usar

1. **Acesse a página**: Navegue até `master/captura_contatos.php` após fazer login
2. **Conecte o WhatsApp**:
   - Clique em "Gerar QR Code"
   - Escaneie o código com seu WhatsApp
   - Aguarde a confirmação de conexão
3. **Capture os Contatos**:
   - Clique em "Capturar da Lista de Contatos" para obter contatos salvos
   - Clique em "Capturar das Conversas" para obter contatos de conversas
4. **Baixe a Lista**: Clique em "Baixar CSV" para exportar os contatos

## Requisitos

- Evolution API configurada e funcionando
- Token da API configurado no cadastro do usuário
- Banco de dados MySQL/MariaDB
- PHP 7.4 ou superior
- Extensões PHP: PDO, cURL, JSON

## Configuração

### 1. Banco de Dados

As tabelas necessárias são criadas automaticamente:
- `conexoes`: Armazena informações de conexão do WhatsApp
- `whatsapp_contacts`: Armazena os contatos capturados

### 2. Evolution API

Configure a URL e API Key em:
- `db/Conexao.php` (use `db/Conexao.example.php` como referência)
- Ou configure no cadastro de cada usuário (campo `tokenapi`)

### 3. Segurança

Para produção:
- Nunca exponha credenciais em arquivos versionados
- Use variáveis de ambiente quando possível
- Mantenha arquivos de configuração fora do web root
- Configure HTTPS para proteger dados em trânsito

## Estrutura de Arquivos

```
master/
├── captura_contatos.php          # Página principal
└── ajax/
    ├── whatsapp_connect.php      # Conecta e gera QR Code
    ├── whatsapp_status.php       # Verifica status da conexão
    ├── whatsapp_capture.php      # Captura contatos e conversas
    ├── whatsapp_disconnect.php   # Desconecta WhatsApp
    ├── download_contacts.php     # Exporta contatos para CSV
    └── clear_contacts.php        # Limpa contatos capturados

db/
├── Conexao.php                   # Configuração do banco de dados
└── Conexao.example.php           # Exemplo de configuração
```

## API Endpoints Utilizados

### Evolution API

- `POST /instance/create` - Cria nova instância
- `GET /instance/connect/{instance}` - Obtém QR Code
- `GET /instance/connectionState/{instance}` - Verifica status
- `GET /chat/findContacts/{instance}` - Lista contatos
- `GET /chat/findChats/{instance}` - Lista conversas
- `DELETE /instance/logout/{instance}` - Desconecta instância

## Solução de Problemas

### Erro ao conectar
- Verifique se a Evolution API está acessível
- Confirme que o API Key está correto
- Verifique as permissões de rede/firewall

### QR Code não aparece
- Verifique logs do console do navegador
- Confirme que a Evolution API está respondendo
- Tente criar uma nova instância

### Contatos não aparecem
- Confirme que o WhatsApp está conectado
- Verifique se há contatos/conversas no WhatsApp
- Consulte os logs do servidor

## Suporte

Para problemas ou dúvidas, verifique:
1. Logs do navegador (Console do desenvolvedor)
2. Logs do servidor PHP
3. Logs da Evolution API
4. Documentação da Evolution API: https://doc.evolution-api.com/
