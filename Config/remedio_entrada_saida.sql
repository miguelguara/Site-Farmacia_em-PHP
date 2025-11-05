-- Dados fictícios de entradas e saídas de remédios (PostgreSQL)
-- Período: última semana relativa a CURRENT_DATE
-- Execute este arquivo diretamente no banco PostgreSQL (não via aplicação)

SET search_path TO public;
BEGIN;

-- =====================
-- Laboratórios
-- =====================
INSERT INTO laboratorios (nome) VALUES
  ('BioNova Laboratórios')
ON CONFLICT (nome) DO NOTHING;

INSERT INTO laboratorios (nome) VALUES
  ('SaúdeMax Pharma')
ON CONFLICT (nome) DO NOTHING;

INSERT INTO laboratorios (nome) VALUES
  ('Genéricos Brasil')
ON CONFLICT (nome) DO NOTHING;

INSERT INTO laboratorios (nome) VALUES
  ('VidaFarma Labs')
ON CONFLICT (nome) DO NOTHING;

-- =====================
-- Classes terapêuticas
-- =====================
INSERT INTO classes_terapeuticas (codigo_classe, nome) VALUES
  (1, 'Analgésicos')
ON CONFLICT (codigo_classe) DO NOTHING;

INSERT INTO classes_terapeuticas (codigo_classe, nome) VALUES
  (2, 'Anti-inflamatórios')
ON CONFLICT (codigo_classe) DO NOTHING;

INSERT INTO classes_terapeuticas (codigo_classe, nome) VALUES
  (3, 'Antibióticos')
ON CONFLICT (codigo_classe) DO NOTHING;

INSERT INTO classes_terapeuticas (codigo_classe, nome) VALUES
  (4, 'Anti-hipertensivos')
ON CONFLICT (codigo_classe) DO NOTHING;

INSERT INTO classes_terapeuticas (codigo_classe, nome) VALUES
  (5, 'Antidiabéticos')
ON CONFLICT (codigo_classe) DO NOTHING;

INSERT INTO classes_terapeuticas (codigo_classe, nome) VALUES
  (6, 'Gastroprotetores')
ON CONFLICT (codigo_classe) DO NOTHING;

INSERT INTO classes_terapeuticas (codigo_classe, nome) VALUES
  (7, 'Broncodilatadores')
ON CONFLICT (codigo_classe) DO NOTHING;

-- =====================
-- Fornecedores
-- =====================
INSERT INTO fornecedores (nome, tipo, contato)
SELECT 'Vida+ Distribuidora', 'compra', 'contato@vidamais.com'
WHERE NOT EXISTS (
  SELECT 1 FROM fornecedores WHERE nome = 'Vida+ Distribuidora' AND tipo = 'compra'
);

INSERT INTO fornecedores (nome, tipo, contato)
SELECT 'Santo Remédio Atacado', 'compra', 'vendas@santoremedio.com'
WHERE NOT EXISTS (
  SELECT 1 FROM fornecedores WHERE nome = 'Santo Remédio Atacado' AND tipo = 'compra'
);

INSERT INTO fornecedores (nome, tipo, contato)
SELECT 'SaúdeCoop', 'parceria', 'coop@saudecoop.org'
WHERE NOT EXISTS (
  SELECT 1 FROM fornecedores WHERE nome = 'SaúdeCoop' AND tipo = 'parceria'
);

-- =====================
-- Pacientes (para dispensações)
-- =====================
INSERT INTO pacientes (nome, cpf, telefone, cidade) VALUES
  ('João Silva', '529.982.247-25', '(11) 90000-0001', 'São Paulo')
ON CONFLICT (cpf) DO NOTHING;

INSERT INTO pacientes (nome, cpf, telefone, cidade) VALUES
  ('Maria Oliveira', '123.456.789-09', '(11) 90000-0002', 'Guarulhos')
ON CONFLICT (cpf) DO NOTHING;

INSERT INTO pacientes (nome, cpf, telefone, cidade) VALUES
  ('Carlos Souza', '529.982.247-25', '(11) 90000-0003', 'Santo André')
ON CONFLICT (cpf) DO NOTHING;

INSERT INTO pacientes (nome, cpf, telefone, cidade) VALUES
  ('Paula Lima', '123.456.789-09', '(11) 90000-0004', 'Campinas')
ON CONFLICT (cpf) DO NOTHING;

-- =====================
-- Medicamentos
-- =====================
-- Paracetamol 750 mg comprimidos (MIP)
INSERT INTO medicamentos (
  codigo, nome, laboratorio_id, classe_terapeutica_id,
  tarja, forma_retirada, forma_fisica,
  apresentacao, unidade_base,
  dosagem_valor, dosagem_unidade,
  generico, limite_minimo, serial_por_classe
) VALUES (
  'PAR-750', 'Paracetamol 750 mg comprimidos',
  (SELECT id FROM laboratorios WHERE nome = 'Genéricos Brasil'),
  (SELECT id FROM classes_terapeuticas WHERE codigo_classe = 1),
  'sem_tarja', 'MIP', 'solida',
  'comprimido', 'comprimido',
  750, 'mg',
  TRUE, 50, 1
)
ON CONFLICT (codigo) DO NOTHING;

-- Dipirona 500 mg comprimidos (MIP)
INSERT INTO medicamentos (
  codigo, nome, laboratorio_id, classe_terapeutica_id,
  tarja, forma_retirada, forma_fisica,
  apresentacao, unidade_base,
  dosagem_valor, dosagem_unidade,
  generico, limite_minimo, serial_por_classe
) VALUES (
  'DIP-500', 'Dipirona 500 mg comprimidos',
  (SELECT id FROM laboratorios WHERE nome = 'Genéricos Brasil'),
  (SELECT id FROM classes_terapeuticas WHERE codigo_classe = 1),
  'sem_tarja', 'MIP', 'solida',
  'comprimido', 'comprimido',
  500, 'mg',
  TRUE, 40, 2
)
ON CONFLICT (codigo) DO NOTHING;

-- Ibuprofeno 400 mg cápsulas (MIP)
INSERT INTO medicamentos (
  codigo, nome, laboratorio_id, classe_terapeutica_id,
  tarja, forma_retirada, forma_fisica,
  apresentacao, unidade_base,
  dosagem_valor, dosagem_unidade,
  generico, limite_minimo, serial_por_classe
) VALUES (
  'IBU-400', 'Ibuprofeno 400 mg cápsulas',
  (SELECT id FROM laboratorios WHERE nome = 'BioNova Laboratórios'),
  (SELECT id FROM classes_terapeuticas WHERE codigo_classe = 2),
  'sem_tarja', 'MIP', 'solida',
  'capsula', 'capsula',
  400, 'mg',
  TRUE, 30, 1
)
ON CONFLICT (codigo) DO NOTHING;

-- Amoxicilina 500 mg cápsulas (prescrição)
INSERT INTO medicamentos (
  codigo, nome, laboratorio_id, classe_terapeutica_id,
  tarja, forma_retirada, forma_fisica,
  apresentacao, unidade_base,
  dosagem_valor, dosagem_unidade,
  generico, limite_minimo, serial_por_classe
) VALUES (
  'AMOX-500', 'Amoxicilina 500 mg cápsulas',
  (SELECT id FROM laboratorios WHERE nome = 'SaúdeMax Pharma'),
  (SELECT id FROM classes_terapeuticas WHERE codigo_classe = 3),
  'tarja_vermelha', 'com_prescricao', 'solida',
  'capsula', 'capsula',
  500, 'mg',
  FALSE, 20, 1
)
ON CONFLICT (codigo) DO NOTHING;

-- Losartana 50 mg comprimidos (prescrição)
INSERT INTO medicamentos (
  codigo, nome, laboratorio_id, classe_terapeutica_id,
  tarja, forma_retirada, forma_fisica,
  apresentacao, unidade_base,
  dosagem_valor, dosagem_unidade,
  generico, limite_minimo, serial_por_classe
) VALUES (
  'LOSA-50', 'Losartana 50 mg comprimidos',
  (SELECT id FROM laboratorios WHERE nome = 'VidaFarma Labs'),
  (SELECT id FROM classes_terapeuticas WHERE codigo_classe = 4),
  'tarja_vermelha', 'com_prescricao', 'solida',
  'comprimido', 'comprimido',
  50, 'mg',
  TRUE, 30, 1
)
ON CONFLICT (codigo) DO NOTHING;

-- Metformina 850 mg comprimidos (prescrição)
INSERT INTO medicamentos (
  codigo, nome, laboratorio_id, classe_terapeutica_id,
  tarja, forma_retirada, forma_fisica,
  apresentacao, unidade_base,
  dosagem_valor, dosagem_unidade,
  generico, limite_minimo, serial_por_classe
) VALUES (
  'METF-850', 'Metformina 850 mg comprimidos',
  (SELECT id FROM laboratorios WHERE nome = 'VidaFarma Labs'),
  (SELECT id FROM classes_terapeuticas WHERE codigo_classe = 5),
  'tarja_vermelha', 'com_prescricao', 'solida',
  'comprimido', 'comprimido',
  850, 'mg',
  TRUE, 30, 1
)
ON CONFLICT (codigo) DO NOTHING;

-- Omeprazol 20 mg cápsulas (prescrição)
INSERT INTO medicamentos (
  codigo, nome, laboratorio_id, classe_terapeutica_id,
  tarja, forma_retirada, forma_fisica,
  apresentacao, unidade_base,
  dosagem_valor, dosagem_unidade,
  generico, limite_minimo, serial_por_classe
) VALUES (
  'OMZ-20', 'Omeprazol 20 mg cápsulas',
  (SELECT id FROM laboratorios WHERE nome = 'SaúdeMax Pharma'),
  (SELECT id FROM classes_terapeuticas WHERE codigo_classe = 6),
  'tarja_vermelha', 'com_prescricao', 'solida',
  'capsula', 'capsula',
  20, 'mg',
  TRUE, 30, 1
)
ON CONFLICT (codigo) DO NOTHING;

-- Salbutamol aerosol 100 mcg (prescrição)
INSERT INTO medicamentos (
  codigo, nome, laboratorio_id, classe_terapeutica_id,
  tarja, forma_retirada, forma_fisica,
  apresentacao, unidade_base,
  dosagem_valor, dosagem_unidade,
  generico, limite_minimo, serial_por_classe
) VALUES (
  'SALB-100', 'Salbutamol 100 mcg aerosol',
  (SELECT id FROM laboratorios WHERE nome = 'BioNova Laboratórios'),
  (SELECT id FROM classes_terapeuticas WHERE codigo_classe = 7),
  'tarja_amarela', 'com_prescricao', 'gasosa',
  'aerosol', 'aerosol',
  100, 'mcg',
  TRUE, 10, 1
)
ON CONFLICT (codigo) DO NOTHING;

-- =====================
-- Lotes (validade futura)
-- =====================
INSERT INTO lotes (medicamento_id, data_fabricacao, validade, nome_comercial, observacao) VALUES
  ((SELECT id FROM medicamentos WHERE codigo = 'PAR-750'), CURRENT_DATE - INTERVAL '2 months', CURRENT_DATE + INTERVAL '8 months', 'Paracetamol 750 mg', 'Lote PAR-2024-A')
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

INSERT INTO lotes (medicamento_id, data_fabricacao, validade, nome_comercial, observacao) VALUES
  ((SELECT id FROM medicamentos WHERE codigo = 'DIP-500'), CURRENT_DATE - INTERVAL '2 months', CURRENT_DATE + INTERVAL '7 months', 'Dipirona 500 mg', 'Lote DIP-2024-A')
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

INSERT INTO lotes (medicamento_id, data_fabricacao, validade, nome_comercial, observacao) VALUES
  ((SELECT id FROM medicamentos WHERE codigo = 'IBU-400'), CURRENT_DATE - INTERVAL '1 months', CURRENT_DATE + INTERVAL '9 months', 'Ibuprofeno 400 mg', 'Lote IBU-2024-A')
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

INSERT INTO lotes (medicamento_id, data_fabricacao, validade, nome_comercial, observacao) VALUES
  ((SELECT id FROM medicamentos WHERE codigo = 'AMOX-500'), CURRENT_DATE - INTERVAL '3 months', CURRENT_DATE + INTERVAL '6 months', 'Amoxicilina 500 mg', 'Lote AMOX-2024-A')
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

INSERT INTO lotes (medicamento_id, data_fabricacao, validade, nome_comercial, observacao) VALUES
  ((SELECT id FROM medicamentos WHERE codigo = 'LOSA-50'), CURRENT_DATE - INTERVAL '3 months', CURRENT_DATE + INTERVAL '10 months', 'Losartana 50 mg', 'Lote LOSA-2024-A')
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

INSERT INTO lotes (medicamento_id, data_fabricacao, validade, nome_comercial, observacao) VALUES
  ((SELECT id FROM medicamentos WHERE codigo = 'METF-850'), CURRENT_DATE - INTERVAL '2 months', CURRENT_DATE + INTERVAL '8 months', 'Metformina 850 mg', 'Lote METF-2024-A')
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

INSERT INTO lotes (medicamento_id, data_fabricacao, validade, nome_comercial, observacao) VALUES
  ((SELECT id FROM medicamentos WHERE codigo = 'OMZ-20'), CURRENT_DATE - INTERVAL '2 months', CURRENT_DATE + INTERVAL '6 months', 'Omeprazol 20 mg', 'Lote OMZ-2024-A')
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

INSERT INTO lotes (medicamento_id, data_fabricacao, validade, nome_comercial, observacao) VALUES
  ((SELECT id FROM medicamentos WHERE codigo = 'SALB-100'), CURRENT_DATE - INTERVAL '1 months', CURRENT_DATE + INTERVAL '12 months', 'Salbutamol 100 mcg', 'Lote SALB-2024-A')
ON CONFLICT (medicamento_id, validade_mes) DO NOTHING;

-- =====================
-- Entradas da última semana
-- Observação: quando unidade != unidade_base, informar unidades_por_embalagem
-- =====================
-- Dia -6
INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '6 days',
  (SELECT id FROM fornecedores WHERE nome = 'Vida+ Distribuidora' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'PAR-750') ORDER BY id DESC LIMIT 1),
  'GB-PAR-001A',
  5, 'caixa', 20, 'novo', 'Reposição semanal - Paracetamol'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '6 days',
  (SELECT id FROM fornecedores WHERE nome = 'Santo Remédio Atacado' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'DIP-500') ORDER BY id DESC LIMIT 1),
  'GB-DIP-001A',
  4, 'caixa', 24, 'novo', 'Reposição semanal - Dipirona'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

-- Dia -5
INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '5 days',
  (SELECT id FROM fornecedores WHERE nome = 'Vida+ Distribuidora' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'IBU-400') ORDER BY id DESC LIMIT 1),
  'BN-IBU-001A',
  6, 'caixa', 12, 'novo', 'Entrada - Ibuprofeno'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '5 days',
  (SELECT id FROM fornecedores WHERE nome = 'SaúdeCoop' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'AMOX-500') ORDER BY id DESC LIMIT 1),
  'SM-AMOX-001A',
  3, 'caixa', 16, 'novo', 'Entrada parceria - Amoxicilina'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

-- Dia -4
INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '4 days',
  (SELECT id FROM fornecedores WHERE nome = 'Santo Remédio Atacado' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'LOSA-50') ORDER BY id DESC LIMIT 1),
  'VF-LOSA-001A',
  4, 'caixa', 30, 'novo', 'Entrada - Losartana'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '4 days',
  (SELECT id FROM fornecedores WHERE nome = 'Vida+ Distribuidora' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'METF-850') ORDER BY id DESC LIMIT 1),
  'VF-METF-001A',
  3, 'caixa', 20, 'novo', 'Entrada - Metformina'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

-- Dia -3
INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '3 days',
  (SELECT id FROM fornecedores WHERE nome = 'SaúdeCoop' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'OMZ-20') ORDER BY id DESC LIMIT 1),
  'SM-OMZ-001A',
  4, 'caixa', 14, 'novo', 'Entrada - Omeprazol'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '3 days',
  (SELECT id FROM fornecedores WHERE nome = 'Vida+ Distribuidora' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'SALB-100') ORDER BY id DESC LIMIT 1),
  'BN-SALB-001A',
  5, 'unidade', 1, 'novo', 'Entrada - Salbutamol (aerosol)'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

-- Dia -2
INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '2 days',
  (SELECT id FROM fornecedores WHERE nome = 'Vida+ Distribuidora' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'PAR-750') ORDER BY id DESC LIMIT 1),
  'GB-PAR-001B',
  3, 'caixa', 20, 'novo', 'Reposição - Paracetamol'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '2 days',
  (SELECT id FROM fornecedores WHERE nome = 'Santo Remédio Atacado' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'DIP-500') ORDER BY id DESC LIMIT 1),
  'GB-DIP-001B',
  2, 'caixa', 24, 'novo', 'Reposição - Dipirona'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

-- Dia -1
INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '1 days',
  (SELECT id FROM fornecedores WHERE nome = 'Vida+ Distribuidora' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'IBU-400') ORDER BY id DESC LIMIT 1),
  'BN-IBU-001B',
  3, 'caixa', 12, 'novo', 'Reposição - Ibuprofeno'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE - INTERVAL '1 days',
  (SELECT id FROM fornecedores WHERE nome = 'Santo Remédio Atacado' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'LOSA-50') ORDER BY id DESC LIMIT 1),
  'VF-LOSA-001B',
  2, 'caixa', 30, 'novo', 'Reposição - Losartana'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

-- Dia 0 (hoje)
INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE,
  (SELECT id FROM fornecedores WHERE nome = 'SaúdeCoop' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'METF-850') ORDER BY id DESC LIMIT 1),
  'VF-METF-001B',
  2, 'caixa', 20, 'novo', 'Reposição - Metformina'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

INSERT INTO entradas (
  data_entrada, fornecedor_id, lote_id, numero_lote_fornecedor,
  quantidade_informada, unidade, unidades_por_embalagem, estado, observacao
) VALUES (
  CURRENT_DATE,
  (SELECT id FROM fornecedores WHERE nome = 'SaúdeCoop' ORDER BY id DESC LIMIT 1),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'OMZ-20') ORDER BY id DESC LIMIT 1),
  'SM-OMZ-001B',
  2, 'caixa', 14, 'novo', 'Reposição - Omeprazol'
)
ON CONFLICT (lote_id, numero_lote_fornecedor) DO NOTHING;

-- =====================
-- Dispensações na última semana (com estoque suficiente)
-- Observação: para medicamentos de prescrição, informar numero_receita
-- =====================
-- Paracetamol (MIP) - comprimidos
INSERT INTO dispensacoes (
  data_dispensa, responsavel, paciente_id, lote_id,
  dosagem, nome_comercial,
  quantidade_informada, unidade, numero_receita
) VALUES (
  CURRENT_DATE - INTERVAL '6 days' + TIME '10:30', 'Farmacêutico',
  (SELECT id FROM pacientes WHERE cpf = '529.982.247-25'),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'PAR-750') ORDER BY id DESC LIMIT 1),
  '750 mg', 'Paracetamol 750 mg',
  20, 'comprimido', NULL
);

INSERT INTO dispensacoes (
  data_dispensa, responsavel, paciente_id, lote_id,
  dosagem, nome_comercial,
  quantidade_informada, unidade, numero_receita
) VALUES (
  CURRENT_DATE - INTERVAL '2 days' + TIME '15:10', 'Farmacêutico',
  (SELECT id FROM pacientes WHERE cpf = '123.456.789-09'),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'PAR-750') ORDER BY id DESC LIMIT 1),
  '750 mg', 'Paracetamol 750 mg',
  10, 'comprimido', NULL
);

-- Dipirona (MIP) - comprimidos
INSERT INTO dispensacoes (
  data_dispensa, responsavel, paciente_id, lote_id,
  dosagem, nome_comercial,
  quantidade_informada, unidade, numero_receita
) VALUES (
  CURRENT_DATE - INTERVAL '5 days' + TIME '09:15', 'Farmacêutico',
  (SELECT id FROM pacientes WHERE cpf = '529.982.247-25'),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'DIP-500') ORDER BY id DESC LIMIT 1),
  '500 mg', 'Dipirona 500 mg',
  16, 'comprimido', NULL
);

-- Ibuprofeno (MIP) - cápsulas
INSERT INTO dispensacoes (
  data_dispensa, responsavel, paciente_id, lote_id,
  dosagem, nome_comercial,
  quantidade_informada, unidade, numero_receita
) VALUES (
  CURRENT_DATE - INTERVAL '3 days' + TIME '11:00', 'Farmacêutico',
  (SELECT id FROM pacientes WHERE cpf = '123.456.789-09'),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'IBU-400') ORDER BY id DESC LIMIT 1),
  '400 mg', 'Ibuprofeno 400 mg',
  12, 'capsula', NULL
);

-- Losartana (prescrição) - comprimidos
INSERT INTO dispensacoes (
  data_dispensa, responsavel, paciente_id, lote_id,
  dosagem, nome_comercial,
  quantidade_informada, unidade, numero_receita
) VALUES (
  CURRENT_DATE - INTERVAL '4 days' + TIME '16:25', 'Farmacêutico',
  (SELECT id FROM pacientes WHERE cpf = '529.982.247-25'),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'LOSA-50') ORDER BY id DESC LIMIT 1),
  '50 mg', 'Losartana 50 mg',
  30, 'comprimido', 'RX-LOSA-2024-001'
);

-- Metformina (prescrição) - comprimidos
INSERT INTO dispensacoes (
  data_dispensa, responsavel, paciente_id, lote_id,
  dosagem, nome_comercial,
  quantidade_informada, unidade, numero_receita
) VALUES (
  CURRENT_DATE - INTERVAL '1 days' + TIME '10:05', 'Farmacêutico',
  (SELECT id FROM pacientes WHERE cpf = '123.456.789-09'),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'METF-850') ORDER BY id DESC LIMIT 1),
  '850 mg', 'Metformina 850 mg',
  20, 'comprimido', 'RX-METF-2024-001'
);

-- Omeprazol (prescrição) - cápsulas
INSERT INTO dispensacoes (
  data_dispensa, responsavel, paciente_id, lote_id,
  dosagem, nome_comercial,
  quantidade_informada, unidade, numero_receita
) VALUES (
  CURRENT_DATE - INTERVAL '2 days' + TIME '14:40', 'Farmacêutico',
  (SELECT id FROM pacientes WHERE cpf = '529.982.247-25'),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'OMZ-20') ORDER BY id DESC LIMIT 1),
  '20 mg', 'Omeprazol 20 mg',
  28, 'capsula', 'RX-OMZ-2024-001'
);

-- Salbutamol (prescrição) - aerosol
INSERT INTO dispensacoes (
  data_dispensa, responsavel, paciente_id, lote_id,
  dosagem, nome_comercial,
  quantidade_informada, unidade, numero_receita
) VALUES (
  CURRENT_DATE - INTERVAL '3 days' + TIME '17:55', 'Farmacêutico',
  (SELECT id FROM pacientes WHERE cpf = '123.456.789-09'),
  (SELECT id FROM lotes WHERE medicamento_id = (SELECT id FROM medicamentos WHERE codigo = 'SALB-100') ORDER BY id DESC LIMIT 1),
  '100 mcg', 'Salbutamol 100 mcg',
  2, 'aerosol', 'RX-SALB-2024-001'
);

COMMIT;