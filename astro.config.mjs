import { defineConfig } from "astro/config";
import starlight from "@astrojs/starlight";
import tailwind from "@astrojs/tailwind";
import vercel from "@astrojs/vercel";
import icon from "astro-icon";

export default defineConfig({
  integrations: [
    starlight({
      title: "Cloudinary Laravel",
      social: {
        github: "https://github.com/cloudinary-community/cloudinary-laravel",
      },
      // sidebar: [
      //   {
      //     label: "Installation",
      //     link: "installation",
      //   },
      //   {
      //     label: "File Storage Driver",
      //     items: [
      //       {
      //         label: "Basic Usage / Configuration",
      //         link: "configuration",
      //       },
      //       {
      //         label: "Examples",
      //         link: "examples",
      //       },
      //       {
      //         label: "Blade Components",
      //         items: [
      //           {
      //             label: "Image Component",
      //             link: "components/image",
      //           },
      //           {
      //             label: "Video Component",
      //             link: "components/video",
      //           },
      //           {
      //             label: "Upload Widget",
      //             link: "components/upload-widget",
      //           },
      //         ],
      //       },
      //       {
      //         label: "Guides",
      //         items: [
      //           {
      //             label: "Image Optimization",
      //             link: "guides/image-optimization",
      //           },
      //           {
      //             label: "Upload Images & Videos",
      //             link: "guides/uploading-images-and-videos",
      //           },
      //           {
      //             label: "Using with other Cloudinary SDKs",
      //             link: "guides/using-with-other-cloudinary-sdks",
      //           },
      //         ],
      //       },
      //     ],
      //   },
      // ],
      customCss: ["./src/styles/tailwind.css"],
      components: {
        Hero: "./src/components/Null.astro",
      },
    }),
    tailwind({
      applyBaseStyles: false,
    }),
    icon(),
  ],

  adapter: vercel({
    webAnalytics: {
      enabled: true,
    },
  }),
});
