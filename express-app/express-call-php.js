const express = require('express');
const axios = require('axios');
const app = express();

app.use(express.json());

const PHP_API_URL = 'http://localhost/47_Patel_MuhammadShakil_A3/php-api.php'; // Replace with your PHP API URL

// GET - Fetch all students from PHP API
app.get('/students', async (req, res) => {
    try {
        const response = await axios.get(PHP_API_URL);
        res.json(response.data);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// POST - Create new student via PHP API
app.post('/students', async (req, res) => {
    try {
        const response = await axios.post(PHP_API_URL, req.body);
        res.json(response.data);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// PUT - Update student via PHP API
app.put('/students/:id', async (req, res) => {
    try {
        const studentData = { ...req.body, id: req.params.id };
        const response = await axios.put(PHP_API_URL, studentData);
        res.json(response.data);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// DELETE - Delete student via PHP API
app.delete('/students/:id', async (req, res) => {
    try {
        const response = await axios.delete(`${PHP_API_URL}?id=${req.params.id}`);
        res.json(response.data);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

const PORT = 5000;
app.listen(PORT, () => {
    console.log(`Express server running on port ${PORT}`);
});
