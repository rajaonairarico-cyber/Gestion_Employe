CREATE TABLE IF NOT EXISTS employe (
    id SERIAL PRIMARY KEY,
    "numEmp" VARCHAR(20) NOT NULL,
    nom VARCHAR(255) NOT NULL,
    emploi VARCHAR(100),
    nb_jours INT NOT NULL,
    taux_journalier NUMERIC(12,2) NOT NULL,
    date_ajout TIMESTAMP,
    date_fin_contrat DATE,
    statut_paiement VARCHAR(20) DEFAULT 'Non payé',
    date_suppression TIMESTAMP NULL
);