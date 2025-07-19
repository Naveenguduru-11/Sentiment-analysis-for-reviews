import sys
import pandas as pd
import json
import numpy as np
import math
from textblob import TextBlob

def analyze_csv(file_path):
    df = pd.read_csv(file_path)

         # Ensure the file has a 'review' column
    if 'review' not in df.columns:
        return {"error": "The CSV file must have a 'review' column."}

    # Function to classify sentiment
    def classify_sentiment(review):
        analysis = TextBlob(review)
        # Polarity ranges from -1 (negative) to 1 (positive)
        if analysis.sentiment.polarity > 0:
            return "Positive"
        elif analysis.sentiment.polarity < 0:
            return "Negative"
        else:
            return "Neutral"

    # Apply sentiment analysis
    df['Sentiment'] = df['review'].apply(classify_sentiment)

    # Separate reviews into positive, negative, and neutral
    positive_reviews = df[df['Sentiment'] == 'Positive']
    negative_reviews = df[df['Sentiment'] == 'Negative']
    neutral_reviews = df[df['Sentiment'] == 'Neutral']

    # Create a JSON response
    sentiment_counts = df['Sentiment'].value_counts().to_dict()
    
    response = {
        "sentiment_counts": sentiment_counts,
        "positive_reviews": positive_reviews['review'].tolist(),
        "negative_reviews": negative_reviews['review'].tolist(),
        "neutral_reviews": neutral_reviews['review'].tolist()
    }

    return {
    "success": True,
    "analysis": response
    }

if __name__ == "__main__":
    try:
        if len(sys.argv) < 2:
            raise ValueError("No file path provided!")
        file_path = sys.argv[1]
        result = analyze_csv(file_path)
        print(json.dumps(result))  # Output valid JSON only
        #print(result)
    except Exception as e:
        print(json.dumps({"success": False, "error": f"Python Script Error: {str(e)}"}))
