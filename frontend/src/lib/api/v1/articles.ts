import type { Article, ArticleMetadata } from "../../../types/api"
import { apiFetch } from "../client"

type _ArticlesApi = {
  getArticles: () => Promise<Article[]>
  getArticlesWithMetadata: () => Promise<{ metadata: ArticleMetadata, articles: Article[] }>
  getArticle: (articleId: number) => Promise<Article | null>
  createArticle: (title: string, content: string) => Promise<Article>
  updateArticle: (articleId: number, title: string, tags: string[], content: string) => Promise<Article>
  publishArticle: (articleId: number, providers: string[]) => Promise<void>
  unpublishArticle: (articleId: number, providers: string[]) => Promise<void>
  deleteArticle: (articleId: number) => Promise<void>
}

type ArticlesWithMetadata = {
  metadata: ArticleMetadata
  articles: Article[]
}

class ArticlesApi implements _ArticlesApi {
  async getArticles(): Promise<Article[]> {
    const response = await apiFetch('/v1/articles', {
      method: 'GET'
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to get articles')
    }

    return data.data.articles as Article[]
  }

  async getArticlesWithMetadata(): Promise<ArticlesWithMetadata> {
    const response = await apiFetch('/v1/articles?includeMetadata=true', {
      method: 'GET'
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to get articles')
    }

    return data.data as ArticlesWithMetadata
  }

  async getArticle(articleId: number): Promise<Article | null> {
    const response = await apiFetch(`/v1/articles/${articleId}`, {
      method: 'GET'
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to get article')
    }

    return data.data.article as Article | null
  }

  async createArticle(title: string, content: string): Promise<Article> {
    const response = await apiFetch('/v1/articles', {
      method: 'POST',
      body: JSON.stringify({ title, content })
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to create article')
    }

    return data.data.article as Article
  }

  async updateArticle(articleId: number, title: string, tags: string[], content: string): Promise<Article> {
    const response = await apiFetch(`/v1/articles/${articleId}`, {
      method: 'PUT',
      body: JSON.stringify({ title, tags, content })
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to update article')
    }

    return data.data.article as Article
  }

  async publishArticle(articleId: number, providers: string[]): Promise<void> {
    const response = await apiFetch(`/v1/articles/${articleId}/publish`, {
      method: 'POST',
      body: JSON.stringify({ providers })
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to publish article')
    }
  }

  async unpublishArticle(articleId: number, providers: string[]): Promise<void> {
    const response = await apiFetch(`/v1/articles/${articleId}/unpublish`, {
      method: 'POST',
      body: JSON.stringify({ providers })
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to unpublish article')
    }
  }

  async deleteArticle(articleId: number): Promise<void> {
    const response = await apiFetch(`/v1/articles/${articleId}`, {
      method: 'DELETE'
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to delete article')
    }
  }
}

export { ArticlesApi, type ArticlesWithMetadata }