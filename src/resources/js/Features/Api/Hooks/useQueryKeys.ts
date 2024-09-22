import { QueryParams as BenefitQueryParams } from '@/Features/Benefit/Hooks/useIndexBenefitsQuery';
import { QueryParams as CharacterQueryParams } from '@/Features/Character/Hooks/useIndexCharactersQuery';
import { QueryParams as StoryQueryParams } from '@/Features/Story/Hooks/useIndexStoriesQuery';
import { QueryParams as GenreQueryParams } from '@/Features/Genre/Hooks/useIndexGenreQuery';
import { QueryParams as LabelQueryParams } from '@/Features/Label/Hooks/useIndexLabelQuery';
import { QueryParams as SeriesQueryParams } from '@/Features/Series/Hooks/useIndexSeriesQuery';

const queryKeys = {
  book: {
    all: 'book',
    index: (params: unknown) => [queryKeys.book.all, params],
    show: (id: number) => [queryKeys.book.all, { id }],
  },

  series: {
    all: 'series',
    show: (id: number) => [queryKeys.series.all, { id }],
    filtered: (queryParams: SeriesQueryParams) => [
      queryKeys.series.all,
      { queryParams },
    ],
  },

  label: {
    all: 'label',
    show: (id: number) => [queryKeys.label.all, { id }],
    filtered: (queryParams: LabelQueryParams) => [
      queryKeys.label.all,
      { queryParams },
    ],
  },

  genre: {
    all: 'genre',
    show: (id: number) => [queryKeys.genre.all, { id }],
    filtered: (queryParams: GenreQueryParams) => [
      queryKeys.genre.all,
      { queryParams },
    ],
  },

  creators: {
    all: 'creators',
    index: (params: unknown) => [queryKeys.creators.all, params],
    show: (id: string | number) => [queryKeys.creators.all, { id }],
  },

  creationTypes: {
    all: 'creation-types',
    show: (id: string | number) => [queryKeys.creationTypes.all, { id }],
  },

  stories: {
    all: 'stories',
    show: (id: number) => [queryKeys.stories.all, { id }],
    filtered: (queryParams: StoryQueryParams) => [
      queryKeys.stories.all,
      { queryParams },
    ],
  },

  characters: {
    all: 'characters',
    show: (id: number) => [queryKeys.characters.all, { id }],
    filtered: (queryParams: CharacterQueryParams) => [
      queryKeys.characters.all,
      { queryParams },
    ],
  },

  stores: {
    all: 'stores',
    show: (id: number) => [queryKeys.stores.all, { id }],
  },

  benefits: {
    all: 'benefits',
    show: (id: number) => [queryKeys.benefits.all, { id }],
    filtered: (queryParams: BenefitQueryParams) => [
      queryKeys.benefits.all,
      { queryParams },
    ],
  },

  externalLinks: {
    all: 'externalLinks',
    index: (params: unknown) => [queryKeys.externalLinks.all, params],
    show: (id: number) => [queryKeys.externalLinks.all, { id }],
  },
};

export const useQueryKeys = () => {
  return queryKeys;
};
