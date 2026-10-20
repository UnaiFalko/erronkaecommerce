USE ecommerce;

UPDATE produktuak
SET irudia = CASE izena
    WHEN 'Aurikular hari gabeak' THEN 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=85'
    WHEN 'Teklatu mekanikoa' THEN 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&w=900&q=85'
    WHEN 'Kamiseta kotoizkoa' THEN 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=85'
    WHEN 'Jaka ur-iragaztezina' THEN 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=900&q=85'
    WHEN 'Kafe-makina' THEN 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=85'
    WHEN 'Lan-lampara LED' THEN 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=900&q=85'
    WHEN 'Yoga esterilla' THEN 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=900&q=85'
    WHEN 'Korrika-zapatilak' THEN 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=85'
    ELSE irudia
END
WHERE irudia IS NULL OR irudia = '';
