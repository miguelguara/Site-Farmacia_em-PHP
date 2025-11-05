-- Seeds idempetentes SEM inserir novos usuários
-- Usa as mesmas entidades do schema, com ON CONFLICT para evitar duplicações
-- Ajusta permissões e papéis, e vincula admin se já existir

SET search_path TO public;
BEGIN;

-- Permissões básicas
INSERT INTO permissoes (codigo, nome) VALUES
  ('acesso_sistema', 'Pode acessar o sistema'),
  ('relatorios', 'Acesso a relatórios'),
  ('entradas', 'Acesso às telas de entradas'),
  ('saidas', 'Acesso às telas de saídas'),
  ('estoque', 'Acesso ao estoque')
ON CONFLICT (codigo) DO NOTHING;

-- Papéis
INSERT INTO papeis (nome, descricao) VALUES
  ('Gestor', 'Gestão geral do sistema'),
  ('Farmacêutico', 'Dispensação e controle de estoque')
ON CONFLICT (nome) DO NOTHING;

-- Laboratório
INSERT INTO laboratorios (nome)
VALUES ('BioNova Laboratórios')
ON CONFLICT (nome) DO NOTHING;

-- Classe terapêutica
INSERT INTO classes_terapeuticas (codigo_classe, nome)
VALUES (2, 'Anti-inflamatórios')
ON CONFLICT (codigo_classe) DO NOTHING;

-- Fornecedor
-- Fornecedor (idempotente sem UNIQUE): usa NOT EXISTS para evitar duplicatas
INSERT INTO fornecedores (nome, tipo, contato)
SELECT 'Vida+ Distribuidora', 'compra', 'contato@vidamais.com'
WHERE NOT EXISTS (
  SELECT 1 FROM fornecedores WHERE nome = 'Vida+ Distribuidora' AND tipo = 'compra'
);

-- Paciente (CPF válido)
INSERT INTO pacientes (nome, cpf, telefone, cidade)
VALUES ('Ana Costa', '123.456.789-09', '(11) 98888-7777', 'Campinas')
ON CONFLICT (cpf) DO NOTHING;

-- Medicamento
INSERT INTO medicamentos (
  codigo, nome, laboratorio_id, classe_terapeutica_id,
  tarja, forma_retirada, forma_fisica,
  apresentacao, unidade_base,
  dosagem_valor, dosagem_unidade,
  generico, limite_minimo, serial_por_classe
)
VALUES (
  'AINF-400', 'Ibuprofeno 400 mg cápsulas',
  (SELECT id FROM laboratorios WHERE nome = 'BioNova Laboratórios'),
  (SELECT id FROM classes_terapeuticas WHERE codigo_classe = 2),
  'sem_tarja', 'MIP', 'solida',
  'capsula', 'capsula',
  400, 'mg',
  TRUE, 20, 1
)
ON CONFLICT (codigo) DO NOTHING;

-- Lote (validade futura)
INSERT INTO lotes (
  medicamento_id, data_fabricacao, validade, nome_comercial, observacao
)
VALUES (
  (SELECT id FROM medicamentos WHERE codigo = 'AINF-400'),
  CURRENT_DATE - INTERVAL '1 month',
  CURRENT_DATE + INTERVAL '9 months',
  'Ibuprofeno 400 mg',
  'Lote de ibuprofeno'
)
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

-- Entrada (10 caixas x 20 comprimidos)
INSERT INTO entradas (
  fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem,
  estado, observacao
)
VALUES (
  (SELECT id FROM fornecedores WHERE nome = 'Vida+ Distribuidora' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'AINF-400') ORDER BY id DESC LIMIT 1),
  'VIDA-IBU-001',
  8, 'caixa', 12,
  'novo', 'Entrada inicial de ibuprofeno'
)
ON CONFLICT DO NOTHING;

-- Dispensação (15 comprimidos)
INSERT INTO dispensacoes (
  responsavel, paciente_id, lote_id,
  dosagem, nome_comercial,
  quantidade_informada, unidade,
  numero_receita
)
VALUES (
  'Farmacêutico',
  (SELECT id FROM pacientes WHERE cpf = '123.456.789-09'),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'AINF-400') ORDER BY id DESC LIMIT 1),
  '400 mg', 'Ibuprofeno 400 mg',
  10, 'capsula',
  NULL
)
ON CONFLICT DO NOTHING;

-- Concede permissões ao papel Administrador
INSERT INTO papeis_permissoes (papel_id, permissao_id)
SELECT p.id, pe.id
FROM papeis p
JOIN permissoes pe ON pe.codigo IN ('acesso_sistema','estoque','entradas','saidas','relatorios')
WHERE p.nome = 'Gestor'
ON CONFLICT DO NOTHING;

-- Permissões para Farmacêutico
INSERT INTO papeis_permissoes (papel_id, permissao_id)
SELECT p.id, pe.id
FROM papeis p
JOIN permissoes pe ON pe.codigo IN ('acesso_sistema','estoque','saidas')
WHERE p.nome = 'Farmacêutico'
ON CONFLICT DO NOTHING;

-- Vincula usuário admin ao papel Administrador (se existir)
INSERT INTO usuarios_papeis (usuario_id, papel_id)
SELECT u.id, p.id
FROM usuarios u
JOIN papeis p ON p.nome = 'Gestor'
WHERE u.login = 'admin'
ON CONFLICT DO NOTHING;

-- Concede permissão direta ao admin (se existir)
INSERT INTO usuarios_permissoes (usuario_id, permissao_id)
SELECT u.id, pe.id
FROM usuarios u
JOIN permissoes pe ON pe.codigo = 'estoque'
WHERE u.login = 'admin'
ON CONFLICT DO NOTHING;

-- Lote que vence no mês atual
INSERT INTO lotes (
  medicamento_id, data_fabricacao, validade, nome_comercial, observacao
)
VALUES (
  (SELECT id FROM medicamentos WHERE codigo = 'AINF-400'),
  CURRENT_DATE - INTERVAL '3 months',
  (date_trunc('month', CURRENT_DATE) + INTERVAL '1 month' - INTERVAL '1 day')::date,
  'Ibuprofeno 400 mg',
  'Lote que vence este mês (ibuprofeno)'
)
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

-- Entrada para o lote que vence neste mês
INSERT INTO entradas (
  fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem,
  estado, observacao
)
VALUES (
  (SELECT id FROM fornecedores WHERE nome = 'Vida+ Distribuidora' ORDER BY id DESC LIMIT 1),
  (
    SELECT id FROM lotes 
    WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'AINF-400')
      AND validade_mes = make_date(date_part('year', CURRENT_DATE)::int, date_part('month', CURRENT_DATE)::int, 1)
    ORDER BY id DESC LIMIT 1
  ),
  'VIDA-IBU-THIS-MONTH',
  3, 'caixa', 12,
  'novo', 'Entrada de lote que vence este mês (ibuprofeno)'
)
ON CONFLICT DO NOTHING;

COMMIT;