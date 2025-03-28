<template>
    <nav aria-label="Page navigation">
      <ul class="pagination">
        <!-- Previous Button -->
        <li
          class="page-item"
          :class="{ disabled: currentPage === 1 }"
          @click="changePage(currentPage - 1)"
        >
          <a class="page-link w-auto bg-transparent" href="#">Previous</a>
        </li>
  
        <!-- Page Numbers with Ellipsis -->
        <li
          v-for="page in visiblePages"
          :key="page"
          class="page-item"          
          :class="{ active: currentPage === page, disabled: page === '...' }"
          @click="page !== '...' && changePage(page)"
        >
          <a class="page-link" href="#" style="opacity:1;">{{ page }}</a>
        </li>
  
        <!-- Next Button -->
        <li
          class="page-item"
          :class="{ disabled: currentPage === lastPage }"
          @click="changePage(currentPage + 1)"
        >
          <a class="page-link w-auto bg-transparent" href="#">Next</a>
        </li>
      </ul>
    </nav>
  </template>
  
  <script>
  import { computed } from "vue";
  
  export default {
    props: {
      total: { type: Number, required: true },
      perPage: { type: Number, required: true },
      currentPage: { type: Number, required: true },
      lastPage: { type: Number, required: true },
    },
    setup(props, { emit }) {
      // Compute visible pages with ellipsis
      const visiblePages = computed(() => {
        const pages = [];
        const maxVisiblePages = 5; // Pages to show in the middle section
  
        // Always show the first and last pages
        pages.push(1);
  
        if (props.lastPage <= maxVisiblePages + 2) {
          // If total pages are small, show all pages
          for (let i = 2; i <= props.lastPage; i++) {
            pages.push(i);
          }
        } else if (props.currentPage <= 3) {
          // Near the beginning
          for (let i = 2; i <= Math.min(4, props.lastPage - 1); i++) {
            pages.push(i);
          }
          pages.push("...");
          pages.push(props.lastPage);
        } else if (props.currentPage >= props.lastPage - 2) {
          // Near the end
          pages.push("...");
          for (let i = props.lastPage - 3; i <= props.lastPage - 1; i++) {
            pages.push(i);
          }
          pages.push(props.lastPage);
        } else {
          // In the middle
          pages.push("...");
          for (let i = props.currentPage - 1; i <= props.currentPage + 1; i++) {
            pages.push(i);
          }
          pages.push("...");
          pages.push(props.lastPage);
        }
  
        return pages;
      });
  
      // Handle page change
      const changePage = (page) => {
        if (page >= 1 && page <= props.lastPage && page !== props.currentPage) {
          emit("update:currentPage", page);
        }
      };
  
      return { visiblePages, changePage };
    },
  };
  </script>
  
  <style>
  .pagination .page-link.bg-transparent:hover{
    background-color: transparent !important;
  }
  .page-item.active{
    pointer-events: none;
  }
  </style>
  