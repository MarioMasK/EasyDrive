INSERT INTO Veicolo (
    telaio, targa, marca, modello, annoImmatricolazione, 
    chilometraggio, tipoAlimentazione, tipoCambio, categoria, 
    prezzoVendita, tariffaNoleggioGiorno, numeroPosti, descrizione, stato
) VALUES 
-- Veicolo 1: Un'auto sportiva in vendita
('ZFA12345678901234', 'AB123CD', 'Fiat', '500 Abarth', 2022, 
15000, 'Benzina', 'Manuale', 'Hatchback', 
22500.00, 85.00, 4, 'Perfetta per la città, scattante e sportiva.', 'Disponibile'),

-- Veicolo 2: Un SUV per il noleggio
('WBA98765432109876', 'EF456GH', 'BMW', 'X3', 2023, 
5000, 'Diesel', 'Automatico', 'SUV', 
45000.00, 120.00, 5, 'Comfort e lusso per i tuoi viaggi lunghi.', 'Disponibile'),

-- Veicolo 3: Un'auto elettrica moderna
('TSL55443322110099', 'EL789EV', 'Tesla', 'Model 3', 2024, 
500, 'Elettrica', 'Automatico', 'Sedan', 
38900.00, 150.00, 5, 'Autonomia eccellente e tecnologia all\'avanguardia.', 'Disponibile'),

-- Veicolo 4: Un furgone per lavoro
('VWF00112233445566', 'ZZ000XX', 'Volkswagen', 'Transporter', 2021, 
45000, 'Diesel', 'Manuale', 'Furgone', 
28000.00, 90.00, 3, 'Ideale per trasporti e carichi pesanti.', 'Disponibile'),

('JT1SUPRA456789012', 'GA123GR', 'Toyota', 'GR Supra', 2023, 8500, 'Benzina', 'Automatico', 'Auto', 65000.00, 300.00, 2, 'Icona delle drifting car giapponesi, motore 3.0 turbo e assetto da pista.', 'Disponibile'),
('WP1CAYENNE9988776', 'PC456CY', 'Porsche', 'Cayenne S', 2024, 2500, 'Ibrida', 'Automatico', 'SUV', 115000.00, 550.00, 5, 'Il SUV di lusso per eccellenza, interni in pelle totale e comfort senza eguali.', 'Disponibile'),
('ZFFPUR0SANGU30102', 'FE001PS', 'Ferrari', 'Purosangue', 2024, 500, 'Benzina', 'Automatico', 'SUV', 420000.00, 1500.00, 4, 'La prima Ferrari a quattro porte, motore V12 aspirato e prestazioni da supercar.', 'Disponibile'),
('WDD1770CLASS_A001', 'MA789CL', 'Mercedes-Benz', 'Classe A', 2023, 15600, 'Ibrida', 'Automatico', 'Auto', 38500.00, 120.00, 5, 'Design moderno e sistema MBUX avanzato. Perfetta per la città e i viaggi.', 'Disponibile'),
('WBYIX3E0000123456', 'BM101IX', 'BMW', 'iX', 2024, 1200, 'Elettrica', 'Automatico', 'SUV', 88000.00, 350.00, 5, 'SUV 100% elettrico con autonomia estesa e tecnologie di guida autonoma.', 'Disponibile'),
('SALRVVELAR5544332', 'LR202VE', 'Land Rover', 'Range Rover Velar', 2022, 28000, 'Diesel', 'Automatico', 'SUV', 72000.00, 280.00, 5, 'Eleganza minimalista e capacità off-road tipiche del marchio Range Rover.', 'Disponibile'),
('ZAR123456STELVIO0', 'AL001ST', 'Alfa Romeo', 'Stelvio Quadrifoglio', 2023, 12000, 'Benzina', 'Automatico', 'SUV', 95000.00, 450.00, 5, 'SUV ad alte prestazioni con motore derivazione Ferrari e trazione integrale Q4.', 'Disponibile'),
('ZAR987654GIULIA00', 'AL002GI', 'Alfa Romeo', 'Giulia Quadrifoglio', 2024, 5000, 'Benzina', 'Automatico', 'Auto', 82000.00, 380.00, 4, 'Berlina sportiva leggendaria, equilibrio perfetto tra potenza e maneggevolezza.', 'Disponibile'),
('VF1CLIO2023004000', 'RE303CL', 'Renault', 'Clio E-Tech', 2023, 9800, 'Ibrida', 'Manuale', 'Sedan', 22500.00, 60.00, 5, 'Ibrida efficiente, agile nel traffico e dai consumi bassissimi.', 'Disponibile'),
('VR3308SW000099887', 'PE404SW', 'Peugeot', '308 SW', 2023, 21000, 'Diesel', 'Automatico', 'Sedan', 34000.00, 100.00, 5, 'Station wagon spaziosa e tecnologica, ideale per la famiglia e il lavoro.', 'Disponibile');

INSERT INTO `Immagine_Veicolo` (
    `url_immagine`, 
    `is_principale`, 
    `telaio_veicolo`
) VALUES 
-- Immagine per Fiat 500 Abarth
('https://www.dropbox.com/scl/fi/qxbtgj9rydukfgj61ovhu/Fiat_500_Abarth_front.jpeg?rlkey=7lpfyibdqtxzk7ep4q49m319z&st=2gps5okl&raw=1', 1, 'ZFA12345678901234'),

-- Immagine per BMW X3
('https://www.dropbox.com/scl/fi/u85c3047kcqccpxw7j2ic/Bmw.jpg?rlkey=h4wb2k6orzr6nxub9ika1f29t&st=47tnwi93&raw=1', 1, 'WBA98765432109876'),

-- Immagine per Tesla Model 3
('https://www.dropbox.com/scl/fi/0sce30p1ye30cmvo4cuwc/tesla.jpg?rlkey=tldc0ng83eovrev2anuejngur&st=lpx4g42x&raw=1', 1, 'TSL55443322110099'),

-- Immagine per Volkswagen Transporter
('https://www.dropbox.com/scl/fi/qxis91wgxtfe36gygixyr/Volkswagen.jpg?rlkey=ee6wn44r9knt8xu776ulv2x0v&st=x68znxbi&raw=1', 1, 'VWF00112233445566'),

('https://www.dropbox.com/scl/fi/8us2d77ozkf0ztjytqp28/toyota-GR-supra.jpg?rlkey=jj0osrd264jd75bhnjy1945s2&st=kn4db6ic&raw=1', 1, 'JT1SUPRA456789012'),
('https://www.dropbox.com/scl/fi/nxdtlmdjngowhg29y6job/porsche-cayanne.jpg?rlkey=anpcqp8ju3ny5wtdv2cmone5t&st=e0zz9xep&raw=1', 1, 'WP1CAYENNE9988776'),
('https://www.dropbox.com/scl/fi/eu57o9b5b1f2av011ul43/ferrari-purosangue.jpg?rlkey=7zbwkzo0krtoeqqotk5yh4f6u&st=5h2qj1zx&raw=1', 1, 'ZFFPUR0SANGU30102'),
('https://www.dropbox.com/scl/fi/3a8rl9yao7ddddeb93ypl/mercedes-class-A.jpg?rlkey=yjpog34mrvj8qih38r79kcmwq&st=diuens0n&raw=1', 1, 'WDD1770CLASS_A001'),
('https://www.dropbox.com/scl/fi/d14djggpoe9qo1jg325tx/BMW-iX.jpg?rlkey=8o7z8nmvisz85e8htn7aj3lxg&st=j7jko99i&raw=1', 1, 'WBYIX3E0000123456'),
('https://www.dropbox.com/scl/fi/c470x7igpufu20ifpzipx/land-rover-velar.jpg?rlkey=liy7aoct2v8vaxbdvmmrpzl0k&st=c9w5fmkw&raw=1', 1, 'SALRVVELAR5544332'),
('https://www.dropbox.com/scl/fi/lzb5v5f5b45qeca03g0kq/alfa-romeo-stelvio.jpg?rlkey=22jch0m9iuzbxoh9j4lslb5o1&st=7772psou&raw=1', 1, 'ZAR123456STELVIO0'),
('https://www.dropbox.com/scl/fi/zs84v5f7vpu563gzdwbuu/alfa-romeo-giulia-quadrifoglio.jpg?rlkey=n4ug376xxpmzdwke2esn9nwxp&st=criqejif&raw=1', 1, 'ZAR987654GIULIA00'),
('https://www.dropbox.com/scl/fi/gesgcxxuy51jb6ermvlfd/renault-clio.jpg?rlkey=kfi0qfguy6qgcpeq96nrk6ibj&st=g4yw2pi5&raw=1', 1, 'VF1CLIO2023004000'),
('https://www.dropbox.com/scl/fi/bgtna4szgmxwbragd7sd4/peugeot-308-sw.jpg?rlkey=0i288f4knsym64ycqyyrqsso2&st=yr63fkz1&raw=1', 1, 'VR3308SW000099887');

-- Inserimento delle icone di sistema
INSERT INTO icone (nome_icona, url_icona, descrizione) VALUES 
('carrello', 'https://www.dropbox.com/scl/fi/hkrldr0u9zasmpbfcr6za/carrello.jpeg?rlkey=5g339hxqj8md8lvvmk18ua94l&st=owmyfaef&raw=1', 'Icona per il carrello nell\'header'),
('utente', 'https://www.dropbox.com/scl/fi/fmfzh9sha5etwxpw6vn55/utente.jpeg?rlkey=0rapikqr39ipq2lpdjseefvts&st=cq2zj69n&raw=1', 'Icona profilo utente/login'),
('catalogo', 'https://www.dropbox.com/scl/fi/k695drvm8wmr0qrikvpkm/catalogo.jpeg?rlkey=a8dsr6v4y3fdb3axv623uey1z&st=l4l59asa&raw=1', 'Icona per la sezione catalogo'),
('login', 'https://www.dropbox.com/scl/fi/p4ozyml0kdxbq0ljcne0o/login.jpeg?rlkey=p4q2a1opxu0phg7drr2p1x94z&st=ng84u1zx&raw=1', 'Immagine per il pulsante o sidebar di login'),
('logout', 'https://www.dropbox.com/scl/fi/shimvpsx9goa255b9g0ol/logout.jpeg?rlkey=5eujnr88yma269s26ffx1nvqy&st=gnrggbft&raw=1', 'Immagine per il pulsante di logout');
