<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تطبيق الأفكار</title>
</head>
<body style="font-family: Arial; padding: 30px; background-color: #f4f4f4;">
<div style="background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3>إضافة فكرة جديدة</h3>
    <form action="/ideas" method="POST">
        @csrf <div style="margin-bottom: 10px;">
            <label>عنوان الفكرة:</label><br>
            <input type="text" name="title" style="width: 100%; padding: 8px; margin-top: 5px;" required>
        </div>

        <div style="margin-bottom: 10px;">
            <label>تفاصيل الفكرة:</label><br>
            <textarea name="description" rows="3" style="width: 100%; padding: 8px; margin-top: 5px;" required></textarea>
        </div>

        <button type="submit" style="background: #0056b3; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">حفظ الفكرة</button>
    </form>
</div>

<h1 style="color: #333;">💡 قائمة الأفكار</h1>
<hr>

<!-- حلقة تكرار للمرور على كل الأفكار -->
@foreach ($ideas as $idea)
    <div style="background: white; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #0056b3;">{{ $idea->title }}</h3>
        <p style="color: #555;">{{ $idea->description }}</p>
        <span style="background-color: #eee; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                الحالة: <strong>{{ $idea->status }}</strong>
            </span>
    </div>
@endforeach

</body>
</html>
