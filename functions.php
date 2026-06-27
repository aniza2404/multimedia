<?php
require_once 'config.php';

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// File handling functions
function getFileType($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'tiff', 'dicom', 'bmp', 'webp'];
    $audio_exts = ['mp3', 'wav', 'ogg', 'flac', 'm4a', 'aac', 'wma'];
    
    if (in_array($ext, $image_exts)) {
        return 'image';
    } elseif (in_array($ext, $audio_exts)) {
        return 'audio';
    } else {
        return 'pdf';
    }
}

function getFileIcon($filetype) {
    if ($filetype === 'image') {
        return 'fa-image';
    } elseif ($filetype === 'audio') {
        return 'fa-music';
    } else {
        return 'fa-file-pdf';
    }
}

function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' bytes';
}

function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// ============================================
// CBR: Content-Based Retrieval - Image Analysis
// ============================================
function analyzeImageCBR($filepath) {
    // Simulated CBR analysis - Replace with actual ML model (e.g., TensorFlow, PyTorch)
    $categories = ['formal', 'non-formal'];
    $confidence = round(rand(75, 99) / 100, 2);
    
    // Simulate processing time
    usleep(rand(200000, 500000));
    
    return [
        'category' => $categories[array_rand($categories)],
        'confidence' => $confidence,
        'method' => 'CNN-ResNet50',
        'process_time' => round((rand(800, 1500) / 1000), 2),
        'metadata' => [
            'dimensions' => '4096 × 2304 px',
            'color_space' => 'sRGB',
            'bit_depth' => '16-bit'
        ]
    ];
}

// ============================================
// TBR: Text-Based Retrieval - PDF to Text + Language Detection
// ============================================
function extractPDFTBR($filepath) {
    // Simulated PDF text extraction - Replace with actual PDF parser (e.g., PDFParser, PyPDF2)
    $sample_text = "This research project investigates the intersection of multi-modal data structures " .
                   "and academic archiving efficiency. By leveraging modern retrieval protocols (CBR, TBR, ABR), " .
                   "the system facilitates a higher throughput for data scientists performing longitudinal studies. " .
                   "Our findings suggest that text-based retrieval remains the bedrock of modern institutional memory, " .
                   "provided the optical character recognition (OCR) fidelity exceeds 98.4% across diverse document formats. " .
                   "The study utilized a comprehensive dataset of academic publications spanning multiple disciplines. " .
                   "Consistent with prior research, we observed significant improvements in retrieval accuracy when " .
                   "combining multiple retrieval approaches. Machine learning algorithms were employed to enhance " .
                   "the semantic understanding of document content. The results demonstrate the effectiveness of " .
                   "multi-modal retrieval systems in academic environments.";
    
    $paragraphs = [
        "The methodology section outlines the experimental design and data collection procedures.",
        "Results indicate a 23% improvement in retrieval efficiency compared to traditional methods.",
        "Discussion focuses on the implications for institutional knowledge management systems.",
        "Future work will explore real-time processing capabilities and scalability."
    ];
    
    $full_text = $sample_text . " " . implode(" ", $paragraphs);
    
    // Simulate processing
    usleep(rand(300000, 600000));
    
    // Language Detection - Replace with actual language detection library
    $languages = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'zh' => 'Chinese',
        'ja' => 'Japanese',
        'ar' => 'Arabic',
        'hi' => 'Hindi'
    ];
    
    // Simulate language detection based on text content
    $detected_lang_code = 'en'; // Default to English
    if (strpos($full_text, 'el') !== false || strpos($full_text, 'la') !== false) {
        $detected_lang_code = 'es';
    } elseif (strpos($full_text, 'le') !== false || strpos($full_text, 'de') !== false) {
        $detected_lang_code = 'fr';
    } elseif (strpos($full_text, 'der') !== false || strpos($full_text, 'die') !== false) {
        $detected_lang_code = 'de';
    }
    
    // Extract keywords and their frequencies
    $keywords = ['research', 'data', 'retrieval', 'system', 'analysis', 'methodology', 'study', 'results', 'machine learning'];
    $keyword_data = [];
    foreach ($keywords as $keyword) {
        $count = substr_count(strtolower($full_text), strtolower($keyword));
        if ($count > 0) {
            $keyword_data[$keyword] = [
                'frequency' => $count,
                'relevance' => round(rand(70, 98) / 100, 2)
            ];
        }
    }
    
    return [
        'text' => $full_text,
        'keywords' => $keyword_data,
        'language_code' => $detected_lang_code,
        'language_name' => $languages[$detected_lang_code] ?? 'Unknown',
        'total_words' => str_word_count($full_text),
        'process_time' => round((rand(500, 1000) / 1000), 2),
        'ocr_fidelity' => round(rand(94, 99) / 100, 2)
    ];
}

// ============================================
// ABR: Attribute-Based Retrieval - Audio Emotion Detection
// ============================================
function analyzeAudioABR($filepath) {
    // Simulated Audio Emotion Analysis - Replace with actual audio processing model
    // This would use libraries like Librosa, TensorFlow, or PyTorch for audio emotion recognition
    
    $emotions = ['happy', 'sad', 'angry', 'neutral', 'surprise'];
    
    // Simulate emotion distribution based on audio features
    // In reality, this would analyze: pitch, tone, rhythm, speech patterns, MFCC features
    $result = [];
    
    // Random but realistic emotion distribution
    $result['neutral'] = round(rand(40, 70) / 100, 2);
    $remaining = 1 - $result['neutral'];
    $other_emotions = array_diff($emotions, ['neutral']);
    
    // Distribute remaining percentage among other emotions
    $total_other = count($other_emotions);
    foreach ($other_emotions as $index => $emotion) {
        if ($index === $total_other - 1) {
            $result[$emotion] = round($remaining, 2);
        } else {
            $val = round(rand(5, 25) / 100, 2);
            $result[$emotion] = $val;
            $remaining -= $val;
        }
    }
    
    // Normalize to ensure sum = 1
    $sum = array_sum($result);
    foreach ($result as $key => $value) {
        $result[$key] = round($value / $sum, 2);
    }
    
    // Audio metadata
    $audio_metadata = [
        'duration' => rand(30, 300) . ' seconds',
        'sample_rate' => '44.1 kHz',
        'bit_depth' => '16-bit',
        'channels' => 'Stereo',
        'spectral_features' => [
            'mfcc_mean' => round(rand(-10, 10) / 10, 2),
            'spectral_centroid' => round(rand(1000, 5000) / 100, 2),
            'zero_crossing_rate' => round(rand(1, 10) / 10, 2)
        ]
    ];
    
    return [
        'emotions' => $result,
        'dominant' => array_keys($result, max($result))[0],
        'confidence' => max($result),
        'audio_metadata' => $audio_metadata,
        'process_time' => round((rand(800, 2000) / 1000), 2),
        'method' => 'CNN-LSTM Audio Classifier'
    ];
}

// Database functions
function getUserFiles($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM multimedia_files WHERE user_id = ? ORDER BY upload_date DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getFileAnalysis($file_id, $type) {
    global $pdo;
    
    if ($type === 'image') {
        $stmt = $pdo->prepare("SELECT * FROM image_analysis WHERE file_id = ?");
        $stmt->execute([$file_id]);
        return $stmt->fetch();
    } elseif ($type === 'pdf') {
        $stmt = $pdo->prepare("SELECT * FROM pdf_documents WHERE file_id = ?");
        $stmt->execute([$file_id]);
        return $stmt->fetch();
    } elseif ($type === 'audio') {
        $stmt = $pdo->prepare("SELECT * FROM emotion_analysis WHERE file_id = ?");
        $stmt->execute([$file_id]);
        return $stmt->fetchAll();
    }
    return null;
}

function getEmotionAnalysis($file_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM emotion_analysis WHERE file_id = ?");
    $stmt->execute([$file_id]);
    return $stmt->fetchAll();
}

function logSearch($user_id, $keyword, $type) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO search_history (user_id, search_keyword, retrieval_type) VALUES (?, ?, ?)");
    return $stmt->execute([$user_id, $keyword, $type]);
}

function getSearchHistory($user_id, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM search_history WHERE user_id = ? ORDER BY search_date DESC LIMIT " . (int)$limit);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getRecentUploads($user_id, $limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM multimedia_files WHERE user_id = ? ORDER BY upload_date DESC LIMIT " . (int)$limit);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getStats($user_id) {
    global $pdo;
    
    // Total files
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM multimedia_files WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_files = $stmt->fetch()['total'];
    
    // Images vs PDFs vs Audio
    $stmt = $pdo->prepare("SELECT file_type, COUNT(*) as count FROM multimedia_files WHERE user_id = ? GROUP BY file_type");
    $stmt->execute([$user_id]);
    $file_types = $stmt->fetchAll();
    
    $images = 0;
    $pdfs = 0;
    $audios = 0;
    foreach ($file_types as $type) {
        if ($type['file_type'] === 'image') $images = $type['count'];
        elseif ($type['file_type'] === 'audio') $audios = $type['count'];
        else $pdfs = $type['count'];
    }
    
    // Total searches
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM search_history WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_searches = $stmt->fetch()['total'];
    
    return [
        'total_files' => $total_files,
        'images' => $images,
        'pdfs' => $pdfs,
        'audios' => $audios,
        'searches' => $total_searches
    ];
}

function getRecentHistory($user_id, $limit = 5) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT sh.*, 
               CASE 
                   WHEN sh.retrieval_type = 'ABR' THEN ea.emotion_type 
                   WHEN sh.retrieval_type = 'CBR' THEN ia.image_category 
                   WHEN sh.retrieval_type = 'TBR' THEN 'PDF Document'
               END as result_type
        FROM search_history sh
        LEFT JOIN emotion_analysis ea ON sh.search_keyword = ea.emotion_type
        LEFT JOIN image_analysis ia ON sh.search_keyword = ia.image_category
        WHERE sh.user_id = ?
        ORDER BY sh.search_date DESC
        LIMIT " . (int)$limit
    );
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function saveFileAnalysis($file_id, $type, $data) {
    global $pdo;
    
    if ($type === 'image') {
        $stmt = $pdo->prepare("INSERT INTO image_analysis (file_id, image_category, confidence_score) VALUES (?, ?, ?)");
        return $stmt->execute([$file_id, $data['category'], $data['confidence']]);
    } elseif ($type === 'pdf') {
        $stmt = $pdo->prepare("INSERT INTO pdf_documents (file_id, extracted_text, detected_language, total_words) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$file_id, $data['text'], $data['language_code'], $data['total_words']]);
    }
    return false;
}

function saveEmotionAnalysis($file_id, $emotions) {
    global $pdo;
    
    foreach ($emotions as $emotion => $score) {
        $stmt = $pdo->prepare("INSERT INTO emotion_analysis (file_id, emotion_type, confidence_score) VALUES (?, ?, ?)");
        $stmt->execute([$file_id, $emotion, $score]);
    }
    return true;
}

function searchFiles($user_id, $keyword, $type) {
    global $pdo;
    
    switch($type) {
        case 'ABR':
            $stmt = $pdo->prepare("
                SELECT m.*, e.emotion_type, e.confidence_score 
                FROM multimedia_files m
                JOIN emotion_analysis e ON m.file_id = e.file_id
                WHERE m.user_id = ? AND e.emotion_type LIKE ?
                ORDER BY e.confidence_score DESC
            ");
            $stmt->execute([$user_id, "%$keyword%"]);
            break;
            
        case 'TBR':
            $stmt = $pdo->prepare("
                SELECT m.*, p.extracted_text, p.detected_language, p.total_words
                FROM multimedia_files m
                JOIN pdf_documents p ON m.file_id = p.file_id
                WHERE m.user_id = ? AND p.extracted_text LIKE ?
                ORDER BY p.total_words DESC
            ");
            $stmt->execute([$user_id, "%$keyword%"]);
            break;
            
        case 'CBR':
            $stmt = $pdo->prepare("
                SELECT m.*, i.image_category, i.confidence_score 
                FROM multimedia_files m
                JOIN image_analysis i ON m.file_id = i.file_id
                WHERE m.user_id = ? AND i.image_category LIKE ?
                ORDER BY i.confidence_score DESC
            ");
            $stmt->execute([$user_id, "%$keyword%"]);
            break;
    }
    
    return $stmt->fetchAll();
}

// Helper function for time elapsed
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
